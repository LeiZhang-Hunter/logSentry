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
        }
    }
}


