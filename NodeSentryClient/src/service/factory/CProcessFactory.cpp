//
// Created by zhanglei on 19-8-16.
//
#include "MainService.h"
using namespace service;

int CProcessFactory::startMonitor(pid_t monitor_process_id,int options){
    monitorStatus = FACTORY_RUN;

    int status;
    pid_t stop_pid;

    while(monitorStatus) {
        stop_pid = waitpid(monitor_process_id, &status, options);
        if(stop_pid > 0)
        {
            this->onMonitor(stop_pid,status);
        }else{
            if(errno == EINTR)
            {
                continue;
            }else{
//                LOG_TRACE(LOG_WARING,false,"CProcessFactory::startMonitor","CProcessFactory->startMonitor occur error");
            }
        }
    }
}

bool CProcessFactory::stopFactory()
{
    monitorStatus = FACTORY_STOP;
}

//设置pid文件的路径
bool CProcessFactory::setPidFile(const char *file) {
    ssize_t res;
    char buf[sizeof(pid_t)+1];

    if(!file)
    {
        LOG_TRACE(LOG_ERROR,false,"CProcessFactory::setPidFile","pid file must not be null");
        return  false;
    }
    strcpy(pidFile,file);

    pidFd = open(pidFile,O_CREAT|O_RDWR,S_IRWXU);

    if(pidFd==0)
    {
        return  true;
    }


    //查看进程是否加了锁，如果加了锁说明程序已经启动了

    guard.l_type = F_WRLCK;
    guard.l_whence = SEEK_SET;
    guard.l_start = 0;
    guard.l_len = 0;

    //加锁，然后判断返回值，如果说已经加过锁了则判断进程已经启动了
    res = fcntl(pidFd,F_SETLK,&guard);
    if(res < 0)
    {
        if(errno == EACCES || errno == EAGAIN)
        {
            LOG_TRACE(LOG_ERROR,false,"CProcessFactory::setPidFile","process has running");
            exit(0);
        }else{
            LOG_TRACE(LOG_ERROR,false,"CProcessFactory::setPidFile","pid file make failed");
            exit(0);
        }
    }

    bzero(buf,sizeof(buf));


    snprintf(buf,sizeof(buf)+1,"%d\n",getpid());

    res = write(pidFd,buf,strlen(buf));

    if(res <= 0)
    {
        LOG_TRACE(LOG_ERROR,false,"CProcessFactory::setPidFile","pid file write failed");
        exit(0);
    }

    return  false;
}


CProcessFactory::~CProcessFactory()
{
    if(pidFd > 0)
    {
        close(pidFd);
    }
}