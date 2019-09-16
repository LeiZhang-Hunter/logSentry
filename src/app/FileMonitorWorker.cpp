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

    threadSocketEvent->hookAdd(CEVENT_READ,onReceive);
}

bool FileMonitorWorker::onConnect() {
    //加套接字加入事件循环
    threadSocketEvent->eventAdd(pipe,EPOLLIN|EPOLLET);
    threadSocketEvent->eventAdd(getSocketHandle()->getSocket(),EPOLLIN|EPOLLET);
}

bool FileMonitorWorker::onClientRead(int fd,char* buf)
{

}
#ifdef _SYS_EPOLL_H
bool FileMonitorWorker::onReceive(struct epoll_event event,void* ptr)
#else
    bool FileMonitorWorker::onReceive(struct pollfd event,void* ptr)
#endif
{

    int fd;
    ssize_t size;
    auto monitor = (FileMonitorWorker *) ptr;

#ifdef _SYS_EPOLL_H
    fd = event.data.fd;
#else
    fd = event.fd;
#endif
    char buf[BUFSIZ];
    if (fd != monitor->pipe) {
        size = read(fd, buf, sizeof(buf));

        if (size == 0) {

            monitor->reconnect();

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
        size = read(fd, &buf, sizeof(buf));
        if(size>0) {
            monitor->onPipe(fd, buf, (size_t) size);
        }
    }
#ifdef _SYS_EPOLL_H
}

#else
}
#endif

//这个是pipe的处理逻辑
void FileMonitorWorker::onPipe(int fd, char *buf2,size_t len) {
    file_read* data;
    ssize_t n;
    char read_buf[BUFSIZ];
    bzero(&read_buf, sizeof(read_buf));
    ssize_t result;
    data = (file_read*)buf2;

    ssize_t offset;



    //读取的buffer 很可能超过最大的buffer 长度如果说超过了需要分次去读
    do{
        if(data->offset > BUFSIZ)
        {
            offset = BUFSIZ;
        }else{
            offset = data->offset;
        }
        n = pread(file_node.file_fd, read_buf,  (size_t)offset, data->begin-offset);
        read_buf[n] = '\0';

        if(n>0)
        {
            result = sendData(client_fd,&read_buf,strlen(read_buf));

            if(result < 0 )
            {
                LOG_TRACE(LOG_ERROR,false,"FileMonitor::onModify","send msg failed");
            }
        }else if(n<0)
        {
            LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","pread fd error");
        }

        data->offset -= n;
    }while(data->offset > 0);

}

bool FileMonitorWorker::onClose() {
    threadSocketEvent->eventDelete(client_fd);
    threadSocketEvent->eventDelete(pipe);
}

FileMonitorWorker::~FileMonitorWorker(){

}