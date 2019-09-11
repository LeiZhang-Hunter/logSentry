//
// Created by zhanglei on 19-8-30.
//
#include "include/MainService.h"
using namespace service;
CThreadSocket::CThreadSocket()
{
    //创建一个socket的句柄
    socketHandle = new CSocket();
    //创建事件集合
    threadSocketEvent = new CEvent();
}

void CThreadSocket::Execute()
{


    bool res;
    int nfds;
    int i;
    char buf[BUFSIZ];
    ssize_t read_size;

    if(run == 1)
    {
        LOG_TRACE(LOG_ERROR,false,"CThreadSocket::Execute","socketHandle Client Has Running");
        return;
    }

    threadSocketEvent->createEvent(2);

    this->onCreate();


    //进行连接
    res = socketHandle->connect(2000);
    if(!res)
    {
        LOG_TRACE(LOG_ERROR,false,"CThreadSocket::Execute","socketHandle->connect error");
        socketHandle->reconnect();
    }

    run = 1;

    //连接成功的时候触发的函数
    this->onConnect();

    threadSocketEvent->eventLoop(this);

}


#ifdef _SYS_EPOLL_H
bool CThreadSocket::reconnect(int fd,uint32_t flags)
#else
bool CThreadSocket::reconnect(int fd,short flags)
#endif
{
    //删除事件
    threadSocketEvent->eventDelete(fd);
    //断线进行重新链接
    socketHandle->reconnect();
    //加入事件循环
    threadSocketEvent->eventAdd(fd,flags,threadSocketEvent->eventFunctionHandle[flags]);
}

ssize_t CThreadSocket::sendData(int fd,void* vptr,size_t n)
{
    bool res;
    send:
    res = socketHandle->send(fd,vptr,n);

    if(!res)
    {
        if(errno == EPIPE and errno == EBADF)
        {
            LOG_TRACE(LOG_ERROR,false,"CThreadSocket::sendData","send msg failed,write error.socket close");
            this->reconnect(fd,CEVENT_READ);
            goto  send;
        }

        return  false;
    }else{
        return true;
    }
}

CSocket* CThreadSocket::getSocketHandle()
{
    return socketHandle;
}

CThreadSocket::~CThreadSocket() {

    //释放掉socket句柄
    if(socketHandle)
        delete(socketHandle);
}