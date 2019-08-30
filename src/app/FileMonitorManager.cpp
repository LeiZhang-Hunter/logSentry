//
// Created by zhanglei on 19-8-21.
//
#include "../../include/Common.h"
using namespace app;
bool FileMonitorManager::start() {
    map<string,string>::iterator it;
    Config* instance = CSingleInstance<Config>::getInstance();
    map<string,map<string,string>>mContent = instance->getConfig();

    int i = 0;
    for(it=monitorConfig.begin();it!=monitorConfig.end();it++)
    {
        FileMonitor* monitor = new FileMonitor();
        monitor->setNotifyPath(it->second);
        monitor->setWorkerNumber(atoi(mContent["server"]["work_thread_number"].c_str()));
        monitorPool.insert(map<int ,FileMonitor*>::value_type(i,monitor));
        monitor->start();
        i++;
    }

    this->startMonitor(-1,0);
}

bool FileMonitorManager::setConfig(map<string,string>config) {
    monitorConfig=config;
}

void FileMonitorManager::onMonitor(pid_t stop_pid,int status)
{
}