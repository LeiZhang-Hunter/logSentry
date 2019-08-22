//
// Created by zhanglei on 19-8-21.
//
#include "../../include/Common.h"

bool FileMonitorManager::start() {
    map<string,string>::iterator it;
    int i = 0;
    for(it=monitorConfig.begin();it!=monitorConfig.end();it++)
    {
        FileMonitor* monitor = new FileMonitor();
        monitorPool.insert(map<int ,FileMonitor*>::value_type(i,monitor));
        monitor->start();
        i++;
    }

    this->waitProcess(-1,0);

}

bool FileMonitorManager::setConfig(map<string,string>config) {
    monitorConfig=config;
}

void FileMonitorManager::onMonitor(pid_t stop_pid,int status)
{
    printf("%d\n",stop_pid);
}