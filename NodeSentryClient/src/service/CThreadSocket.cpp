//
// Created by zhanglei on 19-8-30.
//
#include "MainService.h"
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

    if(run == 1)
    {
        LOG_TRACE(LOG_ERROR,false,"CThreadSocket::Execute","socketHandle Client Has Running");
        return;
    }

    res = threadSocketEvent->createEvent(2);
    if(!res)
    {
        LOG_TRACE(LOG_ERROR,false,"CThreadSocket::Execute","Create Event Error");
        return;
    }

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
bool CThreadSocket::reconnect()
#else
bool CThreadSocket::reconnect(int fd,short flags)
#endif
{
    //删除事件
    onClose();
    //断线进行重新链接
    socketHandle->reconnect();
    onCreate();//创建
    onConnect();//重连
}

ssize_t CThreadSocket::sendData(int fd,void* vptr,size_t n)
{
    ssize_t res;
    send:
    res = socketHandle->send(fd,vptr,n);
    if(res == -1)
    {
        if(errno == EPIPE || errno == EBADF)
        {
            LOG_TRACE(LOG_ERROR,false,"CThreadSocket::sendData","send msg failed,write error.socket close");
            reconnect();
            goto  send;
        }

        return  -1;
    }else{
        return res;
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