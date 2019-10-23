//
// Created by zhanglei on 19-9-19.
//

#include "Common.h"
bool NodeSentry::setMode(int mode)
{
    sentryMode = mode;
    return true;
}

int NodeSentry::getMode() {
    return sentryMode;
}

bool NodeSentry::setWorkerCount(int count)
{
    worker_count = count;
}

int NodeSentry::getWorkerCount()
{
    return worker_count;
}

pid_t NodeSentry::getPid(){
    return pid;
}

map<string, string>::iterator NodeSentry::getConfig(){
    return sentryConfig;
}



//释放掉实例
NodeSentry::~NodeSentry()
{
    switch (sentryMode){
        case LOG_SENTRY:
            if(instance) {
                delete ((FileMonitor *) instance);
            }
            break;
    }
}