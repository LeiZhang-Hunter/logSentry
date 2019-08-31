//
// Created by zhanglei on 19-8-30.
//
#include "../../include/Common.h"
using namespace app;

//构造函数
FileMonitorWorker::FileMonitorWorker(map<string,string> socketConfig,int pipe_fd)
{
    netConfig=socketConfig;
    pipe = pipe_fd;
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
    addEvent(pipe,EPOLLET|EPOLLIN);

    //打开文件的描述符
    fileFd = open("/home/zhanglei/data.log",O_RDONLY);
}

bool FileMonitorWorker::onConnect() {

}

bool FileMonitorWorker::onReceive(int fd,char* buf) {
    file_read* data;
    ssize_t n;
    data = (file_read*)buf;
    cout<<data->begin<<"\n";
    n = pread(fileFd, buf, (size_t)data->offset,data->begin-data->offset);
    buf[n] = '\0';
    if(n>0)
    {
        printf("read:%s\n",buf);
    }else if(n<0)
    {
        LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","pread fd error");
    }
}

bool FileMonitorWorker::onClose() {

}

FileMonitorWorker::~FileMonitorWorker(){

}