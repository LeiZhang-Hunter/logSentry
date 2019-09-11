//
// Created by zhanglei on 19-8-21.
//
#include "../../include/Common.h"

monitor_node file_node;

bool FileMonitorManager::start() {
    map<string,string>::iterator it;
    map<string,map<string,string>>mContent = config_instance->getConfig();

    int i = 0;
    for(it=monitorConfig.begin();it!=monitorConfig.end();it++)
    {
        auto monitor = new FileMonitor();
        monitor->setFileName(it->first);
        monitor->setNotifyPath(it->second);
        monitor->setWorkerNumber(atoi(mContent["server"]["work_thread_number"].c_str()));
        monitor->start();
        processPool[monitor->getPid()] =monitor;
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
    //如果说存在这个实例
    if(processPool[stop_pid])
    {
        printf("stop\n");
        //重新拉起
        auto monitor = new FileMonitor();
        monitor->setFileName(processPool[stop_pid]->getFileName());
        monitor->setNotifyPath(processPool[stop_pid]->getNotifyPath());
        monitor->setWorkerNumber(processPool[stop_pid]->getWorkerNumber());
        monitor->start();
        processPool[stop_pid] =monitor;
        delete processPool[stop_pid];
        processPool.erase(stop_pid);
    }
}

bool FileMonitorManager::stopMonitor()
{
    //循环进程池 删除掉每一个文件监控者
    map<pid_t, FileMonitor *>::iterator it;
    for(it=processPool.begin();it!=processPool.end();it++)
    {
        if(it->second) {
            delete it->second;
        }
    }
}

