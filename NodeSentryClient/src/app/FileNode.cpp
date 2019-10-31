//
// Created by zhanglei on 19-10-20.
//
#include "Common.h"
using namespace app;

//初始化node节点
bool FileNode::initNode(int pipeNumber,const char* path) {
    int pipe[2];//管道的描述符
    int pipe_count;//循环的数字
    int res;//返回结果
    int inotify_fd;

    if(!path)
    {
        return  false;
    }

    if(!os->is_file(path))
    {
        LOG_TRACE(LOG_ERROR,false,"FileNode::initNode","monitor object must be file;path"<<path);
        return false;
    }

    strcpy(monitor_node.path,path);

    //初始化文件node节点
    inotify_fd = inotify_init();

    if(inotify_fd < 0)
    {
        LOG_TRACE(LOG_ERROR,false,"FileNode::initNode","inotify_init "<<path<<" error");
        return false;
    }

    monitor_node.inotify_fd = inotify_fd;

    //如果说是文件
    monitor_node.file_fd = open(path, fileFlags, filePriFlags);

    if (monitor_node.file_fd == -1) {
        LOG_TRACE(LOG_ERROR, false, "FileNode::initNode", "open path:" << path << " fd error");
        return false;
    }

    //监控文件内容修改以及元数据变动
    if(!addMonitor())
    {
        return false;
    }

    monitor_node.pipe_collect = (int(*)[2])calloc((size_t)pipeNumber,sizeof(pipe));

    monitor_node.workerNumberCount = pipeNumber;

    monitor_node.send_number = 0;


    //初始化文件通讯节点的管道
    for(pipe_count=0;pipe_count<pipeNumber;pipe_count++)
    {
        res = socketpair(AF_UNIX,SOCK_DGRAM,0,monitor_node.pipe_collect[pipe_count]);
        if(res == -1)
        {
            LOG_TRACE(LOG_ERROR,false,"FileNode::initNode","socketpair  failed");
            continue;
        }
    }

    //加载文件的初始大小,初始化到文件的节点中
    struct stat buf;
    res = fstat(monitor_node.file_fd, &buf);
    if (res == -1) {
        LOG_TRACE(LOG_ERROR, false, "FileNode::run","fstat fd error");
        return false;
    }
    monitor_node.begin_length = buf.st_size;
    return true;
}

//从inodify中删除掉描述符
bool FileNode::deleteMonitor()
{
    //关闭没有用的描述符
    //从监视中删除
    int res;
    if(monitor_node.wd > 0) {
        res = inotify_rm_watch(monitor_node.inotify_fd, monitor_node.wd);
        if(res == -1)
        {
            LOG_TRACE(LOG_ERROR,false,"FileNode::deleteMonitor","create inotify_rm_watch error;error path:"<<monitor_node.path);
            return false;
        }
    }
    return true;
}

bool FileNode::addMonitor()
{
    monitor_node.wd = inotify_add_watch(monitor_node.inotify_fd,monitor_node.path,monitorFlags);
    if(monitor_node.wd == -1) {
        LOG_TRACE(LOG_ERROR,false,"FileNode::addMonitor","create inotify_add_watch error;error path:"<<monitor_node.path);
        return false;
    }
    return true;
}


bool FileNode::setBeginLen(ssize_t len)
{
    monitor_node.begin_length = len;
}

bool FileNode::reloadNode() {

}


