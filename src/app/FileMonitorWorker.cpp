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
#ifdef _SYS_EPOLL_H
bool FileMonitorWorker::onReceive(struct epoll_event event,void* ptr) {
#else
    bool FileMonitorWorker::onReceive(struct pollfd event,void* ptr) {
#endif


    int fd;
    ssize_t size;
    auto monitor = (FileMonitorWorker *) ptr;

#ifdef _SYS_EPOLL_H
    fd = event.data.fd;
#else
    fd = event.fd;
#endif

    char buf[BUFSIZ];

    size = recv(fd, buf, sizeof(buf), 0);

    if (fd == monitor->client_fd) {
        if (size == 0) {

            monitor->reconnect(fd,CEVENT_READ);

        } else if (size < 0) {
            if (errno == EINTR) {//被信号中断
                return false;
            } else {
                LOG_TRACE(LOG_ERROR,false,"FileMonitorWorker::onReceive","recv failed");
            }

        } else {
            monitor->onClientRead(fd,buf);
        }
    } else {
        buf[size] = '\0';
        monitor->onPipe(fd, buf, (size_t) size);
    }
#ifdef _SYS_EPOLL_H
}

#else
}
#endif

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

    struct protocolStruct dataBuf;
    bzero(&dataBuf,sizeof(protocolStruct));
    strcpy(dataBuf.path,filePath.c_str());
    strcpy(dataBuf.logName,fileName.c_str());
    strcpy(dataBuf.buf,read_buf);

    result = sendData(client_fd,&dataBuf,sizeof(dataBuf));

    if(result < 0 )
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::onModify","send msg failed");
    }

}

bool FileMonitorWorker::onClose() {

}

FileMonitorWorker::~FileMonitorWorker(){

}