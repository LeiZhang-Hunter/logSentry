//
// Created by zhanglei on 19-8-21.
//
#include "../../include/Common.h"

monitor_node file_node;

bool NodeSentryManager::start() {
    map<string,string>::iterator it;
    map<string,map<string,string>>mContent = config_instance->getConfig();

    //监控文件
    for(it=monitorConfig["sentry_log_file"].begin();it!=monitorConfig["sentry_log_file"].end();it++)
    {
        auto sentry = new NodeSentry();
        //设置哨兵的模式
        sentry->setMode(LOG_SENTRY);
        //设置工作的线程数目
        sentry->setWorkerCount(atoi(mContent["sentry"]["file_sentry_thread_number"].c_str()));
        //启动哨兵
        sentry->start<map<string,string>::iterator>(it);
        processPool[sentry->getPid()] =sentry;
    }

    //监控目录
    for(it=monitorConfig["sentry_log_dir"].begin();it!=monitorConfig["sentry_log_dir"].end();it++)
    {
        auto sentry = new NodeSentry();
        //设置哨兵的模式
        sentry->setMode(DIR_SENTRY);
        //设置工作的线程数目
        sentry->setWorkerCount(atoi(mContent["sentry"]["dir_sentry_thread_number"].c_str()));
        //启动哨兵
        sentry->start<map<string,string>::iterator>(it);
        processPool[sentry->getPid()] =sentry;
    }

    this->startMonitor(-1,0);

    this->stopMonitor();
}

bool NodeSentryManager::setConfig(map<string,map<string,string>>config) {
    monitorConfig=config;
}

void NodeSentryManager::onMonitor(pid_t stop_pid,int status)
{
    //如果说存在这个实例
    if(processPool[stop_pid])
    {
        //重新拉起
//        auto monitor = new FileMonitor();
//        monitor->setFileName(processPool[stop_pid]->g->getFileName().c_str());
//        monitor->setNotifyPath(processPool[stop_pid]->getNotifyPath().c_str());
//        monitor->setWorkerNumber(processPool[stop_pid]->getWorkerNumber());
//        monitor->start();
//        delete processPool[stop_pid];
//        processPool.erase(stop_pid);
//        processPool[stop_pid] =monitor;
    }
}

bool NodeSentryManager::stopMonitor()
{
    //循环进程池 删除掉每一个文件监控者
    map<pid_t, NodeSentry *>::iterator it;
    for(it=processPool.begin();it!=processPool.end();)
    {
        int i = it->first;
        delete(it->second);
        it++;
        processPool.erase(i);
    }
}

