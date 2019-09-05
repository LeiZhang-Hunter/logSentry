//
// Created by zhanglei on 19-8-14.
//
#include "include/MainService.h"

CServiceLog::CServiceLog(const char* dir)
{
    logLock = new CMutexLock();

    if(logLock == nullptr)
    {
        return;
    }

    logFd = open(dir,O_RDWR,S_IRWXU);
}

bool CServiceLog::addLog(const char* name,const char* log,const char* file,int file_number)
{
    ssize_t res;
    printf("%s;%s\n",name,log);
    logLock->lock();
    char buf[BUFSIZ];
    bzero(buf,sizeof(buf));
    snprintf(buf,sizeof(buf),"[%s]:%s;file:%s;line:%d\n",name,log,file,file_number);
    printf("%s\n",buf);
    res = write(logFd,buf, sizeof(buf));
    if(res == -1)
    {
        logLock->unLock();
        return  false;
    }
    logLock->unLock();
    return true;
}

CServiceLog::~CServiceLog()
{
    if(logFd > 0)
    {
        close(logFd);
    }

    if(logLock)
    {
        delete logLock;
    }
}