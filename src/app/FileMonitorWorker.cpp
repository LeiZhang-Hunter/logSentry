//
// Created by zhanglei on 19-8-30.
//
#include "../../include/Common.h"
using namespace app;

//构造函数
FileMonitorWorker::FileMonitorWorker(map<string,string> socketConfig)
{
    netConfig=socketConfig;
}

bool FileMonitorWorker::onCreate() {

    CSocket* client_handle = this->getSocketHandle();

    if(!client_handle)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitorWorker::onCreate","client_handle must not be null;line:"<<__LINE__);
        return  false;
    }

    int client_fd = client_handle->getSocket();
    if(client_fd <= 0)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitorWorker::onCreate","client_handle->getSocket failed;errno:"<<errno<<";errormsg:"<<strerror(errno)<<";"<<";line:"<<__LINE__);
        return  false;
    }

    int flag=1;
    setsockopt(client_fd,SOL_SOCKET,SO_REUSEADDR,&flag, sizeof(flag));
    client_handle->setConfig(netConfig);
}

bool FileMonitorWorker::onConnect() {

}

bool FileMonitorWorker::onReceive(int fd,char* buf) {
    printf("111\n");
}

bool FileMonitorWorker::onClose() {

}

FileMonitorWorker::~FileMonitorWorker(){

}