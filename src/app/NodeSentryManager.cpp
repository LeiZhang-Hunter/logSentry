//
// Created by zhanglei on 19-8-21.
//
#include "Common.h"


bool NodeSentryManager::start() {
    map<string,string>::iterator it;
    map<string,map<string,string>>mContent = config_instance->getConfig();
    int fileWorkerNumber;
    int dirWorkerNumber;

    fileWorkerNumber = atoi(mContent["sentry"]["file_sentry_thread_number"].c_str());

    //监控文件
    if(fileWorkerNumber > 0) {
        for (it = monitorConfig["sentry_log_file"].begin(); it != monitorConfig["sentry_log_file"].end(); it++) {
            auto sentry = new NodeSentry();
            //设置哨兵的模式
            sentry->setMode(LOG_SENTRY);
            //设置工作的线程数目
            sentry->setWorkerCount(fileWorkerNumber);
            //启动哨兵
            sentry->start(it);
            processPool[sentry->getPid()] = sentry;
        }
    }

    //监控目录
    dirWorkerNumber = atoi(mContent["sentry"]["dir_sentry_thread_number"].c_str());
    if(dirWorkerNumber>0) {
        for (it = monitorConfig["sentry_log_dir"].begin(); it != monitorConfig["sentry_log_dir"].end(); it++) {
            auto sentry = new NodeSentry();
            //设置哨兵的模式
            sentry->setMode(DIR_SENTRY);
            //设置工作的线程数目
            sentry->setWorkerCount(dirWorkerNumber);
            //启动哨兵
            sentry->start(it);
            processPool[sentry->getPid()] = sentry;
        }
    }

    //如果说都没有设置
    if(!fileWorkerNumber && !dirWorkerNumber)
    {
        LOG_TRACE(LOG_ERROR,false,"NodeSentryManager::start","worker number set error");
    }

    this->startMonitor(-1,0);

    this->stopMonitor();
}

bool NodeSentryManager::setConfig(map<string,map<string,string>>config) {
    monitorConfig=config;
}

void NodeSentryManager::onMonitor(pid_t stop_pid,int status)
{
    //如果说存在这个进程在进程池里面
    if(processPool[stop_pid])
    {
        //重新拉起
        NodeSentry* sentry;
        int mode = processPool[stop_pid]->getMode();
        int workerNumber = processPool[stop_pid]->getWorkerCount();
        switch (mode)
        {
            case LOG_SENTRY:
            case DIR_SENTRY:
                sentry = new NodeSentry();
                sentry->setMode(mode);
                sentry->setWorkerCount(workerNumber);
                sentry->start(processPool[stop_pid]->getConfig());
                //放入到进程池子里面
                processPool[sentry->getPid()] = sentry;
                break;
        }
        delete processPool[stop_pid];
        processPool.erase(stop_pid);
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

