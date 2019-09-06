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

    logFd = open("/home/zhanglei/log.log",O_RDWR|O_CREAT,S_IRWXU);
    if(logFd == -1)
    {
        printf("failed\n");
        printf("errno:%d,msg:%s\n",errno,strerror(errno));
        return  ;
    }
}

/**
 * 写入日志
 * @param name
 * @param log
 * @param file
 * @param file_number
 * @return
 */
bool CServiceLog::addLog(const char* name,const char* log,const char* file,int file_number)
{
    ssize_t res;
    logLock->lock();
    char buf[BUFSIZ];
    bzero(buf,sizeof(buf));
    snprintf(buf,sizeof(buf),"[%s]:%s;errno:%d;errormsg:%s;file:%s;line:%d\n",name,log,errno,strerror(errno),file,file_number);
    cout<<buf;
    res = write(logFd,"aaa\n", strlen("aaa\n"));
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