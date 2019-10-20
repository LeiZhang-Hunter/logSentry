//
// Created by zhanglei on 19-9-19.
//

#include "Common.h"
DirNode MonitorDirNode;

DirMonitor::DirMonitor()
{

}


void DirMonitor::start() {
    //创建worker
//    int res = this->createProcess();
//
//    if(res != 0)
//    {
//        LOG_TRACE(LOG_ERROR,false,"FileMonitor::start","create process error");
//        return;
//    }
    run();
}

bool DirMonitor::setFileName(const char* file_name)
{
    fileName = (file_name);
}

string DirMonitor::getFileName()
{
    return fileName;
}


bool DirMonitor::setWorkerNumber(int number)
{
    workerNumber = number;
}

bool DirMonitor::setNotifyPath(const char* path)
{
    monitorPath = path;
}

string DirMonitor::getNotifyPath()
{
    return monitorPath;
}

int DirMonitor::getWorkerNumber()
{
    return workerNumber;
}

//执行程序的入口
void DirMonitor::run()
{
    bool init_result;
    int workerNum;

    //初始化节点
    init_result = MonitorDirNode.initNode(workerNumber,monitorPath.c_str());
    if(!init_result)
    {
        LOG_TRACE(LOG_ERROR,false,"DirMonitor::run","DirNode->initNode failed;path:");
        return;
    }
    eventInstance = new CEvent();

    eventInstance->createEvent(10);

    eventInstance->hookAdd(CEVENT_READ,onChange);

    eventInstance->hookAdd(CEVENT_WRITE,onSend);


    eventInstance->eventAdd(MonitorDirNode.monitor_node.inotify_fd,EPOLLET|EPOLLIN);

    string buffer;
    DirMonitorWorker* worker_object;

    //在站上申请一个内存池
    map<string,map<string,string>>mContent = config_instance->getConfig();

    //创建目录监控处理进程
    for(workerNum = 0;workerNum<workerNumber;workerNum++)
    {
        //加入可写事件的监控
        eventInstance->eventAdd(MonitorDirNode.monitor_node.pipe_collect[workerNum][1],EPOLLET|EPOLLIN);

        //创建工作线程用来处理变化
        worker_object = new DirMonitorWorker(mContent["server"],MonitorDirNode.monitor_node.pipe_collect[workerNum][0]);
        worker_object->SetDaemonize();
        worker_object->Start();
    }

    eventInstance->eventLoop(this);
}

bool DirMonitor::onChange(struct epoll_event eventData,void* ptr)
{
    char buf[BUFSIZ];
    ssize_t read_size;
    int fd;
    int i = 0;
    struct inotify_event* event;
    auto dir_monitor = (DirMonitor*)ptr;
    int change_fd;
    int pipe_number;
    int pipeFd;

#ifdef _SYS_EPOLL_H
    fd = eventData.data.fd;
#else
    fd = eventData.fd;
#endif

    read_size = read(fd,buf,BUFSIZ);

    if(read_size > 0)
    {
        while(i<read_size){
            event = (struct inotify_event*)&buf[i];
            //文件发生变动
            if(event->mask & IN_MODIFY)
            {
                printf("IN_MODIFY;path:%s\n",event->name);
                change_fd = dir_monitor->fileDirPool[event->name];
                //将这个变化事件加入队列池
                dir_monitor->eventPool.push_front(change_fd);

                //随机获取一个管道发送
                pipe_number = dir_monitor->send_number%dir_monitor->getWorkerNumber();

                pipeFd = *(MonitorDirNode.monitor_node.pipe_collect[pipe_number]+1);

                //把事件设置为可写事件
                dir_monitor->eventInstance->eventUpdate(pipeFd,EPOLLOUT|EPOLLET);

                dir_monitor->send_number++;

                printf("pipeFd:%d;pipe_number:%d;number:%d\n",pipeFd,pipe_number,dir_monitor->send_number);

            }else if(event->mask & IN_ATTRIB)
            {
                //文件属性发生变动
                printf("IN_ATTRIB;path:%s\n",event->name);

            }else if(event->mask & IN_DELETE || event->mask & IN_DELETE_SELF)
            {//文件发生删除操作
                printf("IN_DELETE;path:%s\n",event->name);
                MonitorDirNode.deleteFileToPool(event->name);

            }else if(event->mask & IN_CREATE)
            {
                //文件发生创建操作
                MonitorDirNode.addFileToPool(event->name);
                printf("IN_CREATE;path:%s\n",event->name);
            }else if(event->mask & IN_IGNORED)
            {//事件被移除掉会到这里进行通知
                printf("IN_IGNORED;path:%s\n",event->name);
            }
            i+=(sizeof(struct inotify_event)+event->len);
        }
    }
}


//数据应该发送的时候
bool DirMonitor::onSend(struct epoll_event eventData, void *ptr)
{
    int change_fd;
    int event_fd;
    int res;
    ssize_t  write_size;
    auto dir_monitor = (DirMonitor*)ptr;
    ssize_t readLen;
    while((change_fd = dir_monitor->eventPool.back()))
    {
        //观察文件的变化尺寸
        dir_monitor->eventPool.pop_back();
        struct stat file_buffer;
        event_fd = eventData.data.fd;
        res = fstat(change_fd, &file_buffer);
        if (res == -1) {
            LOG_TRACE(LOG_ERROR, false, "DirMonitor::onModify","fstat fd error");
            return false;
        }

        auto dir_file_node = dir_monitor->fileDataPool[change_fd];

        //申请一个新的结构体
        readLen = file_buffer.st_size-dir_file_node.begin;

        dir_monitor->fileDataPool[change_fd].begin = file_buffer.st_size;

        //给要读的偏移量赋值
        dir_file_node.begin = file_buffer.st_size;

        dir_file_node.offset = readLen;

        write_size = write(event_fd,&dir_file_node,sizeof(file_dir_data));
        if(write_size<=0)
        {
            LOG_TRACE(LOG_ERROR, false, "DirMonitor::onSend","Write Pipe Fd Error");
        }
    }
}
