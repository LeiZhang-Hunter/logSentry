//
// Created by zhanglei on 19-9-19.
//

#include "Common.h"
bool NodeSentry::setMode(int mode)
{
    sentryMode = mode;
    return true;
}

bool NodeSentry::setWorkerCount(int count)
{
    worker_count = count;
}

int NodeSentry::getWorkerCount(int count)
{
    return worker_count;
}

pid_t NodeSentry::getPid(){
    return pid;
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