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

    logFd = open(dir,O_RDWR|O_CREAT|O_APPEND,S_IRWXU);
    if(logFd == -1)
    {
        cout<<"[CServiceLog::CServiceLog]:open log file failed;errno"<<errno<<";errormsg:"<<strerror(errno)<<";file:"<<__FILE__<<";line:"<<__LINE__;
        exit(-1);
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
    res = write(logFd,buf, strlen(buf));
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