//
// Created by zhanglei on 19-8-30.
//
#include "../../include/Common.h"

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
        LOG_TRACE(LOG_ERROR,false,"FileMonitorWorker::onCreate","client_handle must not be null;");
        return  false;
    }

    client_fd = client_handle->getSocket();
    if(client_fd <= 0)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitorWorker::onCreate","client_handle->getSocket failed;");
        return  false;
    }

    int flag=1;
    setsockopt(client_fd,SOL_SOCKET,SO_REUSEADDR,&flag, sizeof(flag));

    client_handle->setConfig(netConfig["ip"].c_str(),netConfig["port"].c_str());
}

bool FileMonitorWorker::onConnect() {
    //加套接字加入事件循环
    threadSocketEvent->eventAdd(pipe,CEVENT_READ,onReceive);
    threadSocketEvent->eventAdd(getSocketHandle()->getSocket(),CEVENT_READ,onReceive);
}

bool FileMonitorWorker::onClientRead(int fd,char* buf)
{

}

bool FileMonitorWorker::onReceive(struct pollfd event,void* ptr) {

    auto monitor = (FileMonitorWorker*)ptr;

    char buf[512];

    if(event.fd == monitor->client_fd)
    {
        monitor->onClientRead(event.fd,buf);
    }else{
        monitor->onPipe(event.fd,buf,100);
    }
}

//这个是pipe的处理逻辑
void FileMonitorWorker::onPipe(int fd, char *buf,size_t len) {
    file_read* data;
    ssize_t n;
    char read_buf[BUFSIZ];
    ssize_t result;
    data = (file_read*)buf;
    n = pread(file_node.file_fd, read_buf, (size_t)data->offset,data->begin-data->offset);
    read_buf[n] = '\0';
    if(n>0)
    {
    }else if(n<0)
    {
        LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","pread fd error");
    }

    result = sendData(client_fd,read_buf,(size_t)n);

    if(result < 0 )
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::onModify","send msg failed");
    }

}

bool FileMonitorWorker::onClose() {

}

FileMonitorWorker::~FileMonitorWorker(){

}