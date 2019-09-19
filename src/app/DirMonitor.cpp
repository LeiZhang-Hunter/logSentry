//
// Created by zhanglei on 19-9-19.
//

#include "../../include/Common.h"


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

    inotify_wd = inotify_add_watch(inotify_fd,monitorPath.c_str(),IN_ALL_EVENTS);
    if(inotify_wd <= 0)
    {
        LOG_TRACE(LOG_ERROR,false,"DirMonitor::run","inotify add watch error");
        return;
    }

    eventInstance = new CEvent();

    eventInstance->eventAdd(inotify_fd,EPOLLET|EPOLLIN);
}
