//
// Created by zhanglei on 19-10-20.
//
#include "Common.h"
using namespace app;
bool DirNode::initNode(int pipeNumber,const char* path)
{
    struct dirent *dirEntry;//读取目录的子文件句柄
    int res;//存储返回结果的变量
    file_dir_data dataUnit;//目录文件池的存储结构体
    int pipe_count;//循环创建管道的计数器
    int pipe[2];//unix 管道的地址

    if(!os->is_dir(path))
    {
        LOG_TRACE(LOG_ERROR,false,"DirNode::initNode","monitor object must be dir;path"<<path);
        return false;
    }

    monitor_node.monitor_dir = opendir(path);
    if(!monitor_node.monitor_dir)
    {
        LOG_TRACE(LOG_ERROR,false,"FileNode::initNode","get opendir failed;path:"<<path);
        return false;
    }

    strcpy(monitor_node.path,path);

    //遍历加入文件池中
    while((dirEntry = readdir(monitor_node.monitor_dir)))
    {
        if(dirEntry->d_type == DT_REG)
        {
            addFileToPool(dirEntry->d_name);
        }
    }


    //初始化文件inode节点的监控
    monitor_node.inotify_fd = inotify_init();

    if(monitor_node.inotify_fd < 0)
    {
        LOG_TRACE(LOG_ERROR,false,"DirNode::initNode","create process error");
        return false;
    }

    //监控文件内容修改以及元数据变动
    if(!addMonitor())
    {
        return false;
    }

    monitor_node.pipe_collect = (int(*)[2])calloc((size_t)pipeNumber,sizeof(pipe));


    for(pipe_count=0;pipe_count<pipeNumber;pipe_count++)
    {
        res = socketpair(AF_UNIX,SOCK_DGRAM,0,monitor_node.pipe_collect[pipe_count]);
        if(res == -1)
        {
            LOG_TRACE(LOG_ERROR,false,"FileNode::initNode","socketpair  failed");
            continue;
        }
    }

    return  true;
}

//从inodify中删除掉描述符
bool DirNode::deleteMonitor()
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

bool DirNode::deleteFileToPool(const char* name)
{
    if(fileDirPool.find(name) != fileDirPool.end())
    {
        //关闭掉旧的没用的描述符
        close(fileDirPool[name].file_fd);
        fileDirPool.erase(name);
        return  true;
    }

    return  false;
}

file_dir_data DirNode::getFileToPool(const char* name)
{
    return fileDirPool[name];
}

bool DirNode::setFileNodeLengthByPool(const char* name,size_t length)
{
    return  fileDirPool[name].begin = length;
}

bool DirNode::addFileToPool(const char* name)
{
    int monitor_file_fd;//目录下面文件的fd
    struct stat file_state;//文件状态的结构体
    file_dir_data dataUnit;//目录文件池的存储结构体
    int res;
    char file_path[PATH_MAX+NAME_MAX];//目录的路径
    if(fileDirPool.find(name) != fileDirPool.end())
    {
        LOG_TRACE(LOG_WARING,false,"FileNode::addFileToPool","fileDirPool find name: "<<name<<" exist;error path:"<<monitor_node.path);
        return  false;
    }

    if(!name)
    {
        LOG_TRACE(LOG_ERROR,false,"FileNode::addFileToPool","fileDirPool name: is null;error path:"<<monitor_node.path);
        return  false;
    }

    bzero(&file_path,sizeof(file_path));
    snprintf(file_path,sizeof(file_path),"%s/%s",monitor_node.path,name);

    monitor_file_fd = open(file_path,O_CREAT|O_RDWR,S_IRUSR);
    if(monitor_file_fd <= 0)
    {
        LOG_TRACE(LOG_ERROR,false,"FileNode::addFileToPool","open file error:"<<file_path);
        return  false;
    }

    res = fstat(monitor_file_fd,&file_state);
    if(res == -1)
    {
        LOG_TRACE(LOG_ERROR,false,"FileNode::addFileToPool","fstat file error");
        return  false;
    }


    bzero(&dataUnit, sizeof(dataUnit));

    dataUnit.begin = file_state.st_size;

    dataUnit.file_fd = monitor_file_fd;

    //查看文件这个时候的大小来记录begin值，当发生变化的时候可以进行变动更新
    strcpy(dataUnit.name,name);

    //加入到目录的文件池中
    fileDirPool[name] = dataUnit;

    return true;
}


bool DirNode::addMonitor()
{
    monitor_node.wd = inotify_add_watch(monitor_node.inotify_fd,monitor_node.path,monitorFlags);
    if(monitor_node.wd == -1) {
        LOG_TRACE(LOG_ERROR,false,"FileNode::addMonitor","create inotify_add_watch error;error path:"<<monitor_node.path);
        return false;
    }
    return true;
}
