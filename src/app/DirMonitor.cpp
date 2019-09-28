//
// Created by zhanglei on 19-9-19.
//

#include "Common.h"


void DirMonitor::start() {
    //创建worker
    int res = this->createProcess();

    if(res != 0)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::start","create process error");
        return;
    }
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

    //创建一个wd
    inotify_fd = inotify_init();
    if(inotify_fd<= 0)
    {
        LOG_TRACE(LOG_ERROR,false,"DirMonitor::run","inotify init failed");
        return;
    }
    inotify_wd = inotify_add_watch(inotify_fd,monitorPath.c_str(),IN_CREATE|IN_DELETE_SELF|IN_DELETE|IN_MODIFY|IN_ATTRIB);
    if(inotify_wd <= 0)
    {
        LOG_TRACE(LOG_ERROR,false,"DirMonitor::run","inotify add watch error");
        return;
    }

    eventInstance = new CEvent();

    eventInstance->createEvent(10);

    eventInstance->hookAdd(CEVENT_READ,onChange);

    eventInstance->eventAdd(inotify_fd,EPOLLET|EPOLLIN);

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

            }else if(event->mask & IN_ATTRIB)
            {//文件属性发生变动

            }else if(event->mask & IN_DELETE || event->mask & IN_DELETE_SELF)
            {//文件发生删除操作

            }else if(event->mask & IN_CREATE)
            {//文件发生创建操作

            }
            i+=(sizeof(struct inotify_event)+event->len);
        }
    }
}