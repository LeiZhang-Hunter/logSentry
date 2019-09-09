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
    addEvent(pipe,EPOLLET|EPOLLIN|EPOLLERR|EPOLLHUP);
    addEvent(this->getSocketHandle()->getSocket(),EPOLLET|EPOLLIN|EPOLLERR|EPOLLHUP);
}

bool FileMonitorWorker::onClientRead(int fd,char* buf)
{

}

bool FileMonitorWorker::onReceive(int fd,char* buf,size_t len) {

    if(fd == client_fd)
    {
        this->onClientRead(fd,buf);
    }else{
        this->onPipe(fd,buf,len);
    }
}

//这个是pipe的处理逻辑
void FileMonitorWorker::onPipe(int fd, char *buf,size_t len) {
    file_read* data;
    ssize_t n;
    char read_buf[BUFSIZ];
    bool result;
    data = (file_read*)buf;
    cout<<data->begin<<"\n";
    n = pread(file_node.file_fd, read_buf, (size_t)data->offset,data->begin-data->offset);
    printf("read size:%ld;begin:%ld;end:%ld\n",n,data->offset,data->begin-data->offset);
    read_buf[n] = '\0';
    if(n>0)
    {
        printf("read:%s\n",read_buf);
    }else if(n<0)
    {
        LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","pread fd error");
    }

    result = getSocketHandle()->send(client_fd,read_buf,(size_t)n);

    if(!result)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::onModify","send msg failed");
    }

}

bool FileMonitorWorker::onClose() {

}

FileMonitorWorker::~FileMonitorWorker(){

}