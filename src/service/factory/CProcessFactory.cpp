//
// Created by zhanglei on 19-8-16.
//
#include "../include/MainService.h"
using namespace service;

int CProcessFactory::startMonitor(pid_t monitor_process_id,int options){
    monitorStatus = MONITOR_RUN;

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

            }
        }
    }
}

//设置pid文件的路径
bool CProcessFactory::setPidFile(char *file) {
    strcpy(pidFile,file);

    int fd = open(pidFile,O_CREAT|O_RDWR,S_IRWXU);

    if(fd==0)
    {
        return  true;
    }

    LOG_TRACE(LOG_ERROR,false,"CProcessFactory::setPidFile","pid file open filed");

    //查看进程是否加了锁，如果加了锁说明程序已经启动了

    close(fd);
    return  false;
}
