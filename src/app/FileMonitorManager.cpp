//
// Created by zhanglei on 19-8-21.
//
#include "../../include/Common.h"
using namespace app;

monitor_node file_node;

bool FileMonitorManager::start() {
    map<string,string>::iterator it;
    map<string,map<string,string>>mContent = config_instance->getConfig();

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

    this->stopMonitor();
}

bool FileMonitorManager::setConfig(map<string,string>config) {
    monitorConfig=config;
}

void FileMonitorManager::onMonitor(pid_t stop_pid,int status)
{

}

bool FileMonitorManager::stopMonitor()
{
    //循环进程池 删除掉每一个文件监控者
    map<int, FileMonitor *>::iterator it;
    for(it=monitorPool.begin();it!=monitorPool.end();it++)
    {
        if(it->second) {
            delete it->second;
        }
    }
}

