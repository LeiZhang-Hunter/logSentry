//
// Created by zhanglei on 19-8-30.
//
#include "include/MainService.h"
using namespace service;
CThreadSocket::CThreadSocket()
{
    //创建一个socket的句柄
    socketHandle = new CSocket();
    //创建epoll
    eventfd = epoll_create(3);

    if(eventfd == -1)
    {
        LOG_TRACE(LOG_ERROR,false,"CThreadSocket::CThreadSocket","");
        return;
    }

    //创建一个事件集
    eventCollect = (struct epoll_event*)calloc(2,sizeof(struct epoll_event));
    bzero(eventCollect,sizeof(struct epoll_event)*2);
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

    while(run)
    {
        nfds = epoll_wait(eventfd,eventCollect,512,-1);

        //返回准备就绪的描述符
        if(nfds>0)
        {
            for(i=0;i<nfds;i++)
            {

                //触发可读事件
                if(eventCollect[i].events&EPOLLIN)
                {
                    read_size = socketHandle->recv(eventCollect[i].data.fd,buf, sizeof(buf));
                    printf("read_size:%ld\n",read_size);

                    if(read_size == -1)
                    {
                        LOG_TRACE(LOG_ERROR,false,"CThreadSocket::Execute","socketHandle->recv error");
                    }else if(read_size == 0){
                        LOG_TRACE(LOG_ERROR,false,"CThreadSocket::Execute","client socket has closed");
                        reconnect(eventCollect[i].data.fd);
                    }else{
                        buf[read_size] = '\0';
                        this->onReceive(eventCollect[i].data.fd,buf,sizeof(buf));
                    }
                }else if(eventCollect[i].events & (EPOLLRDHUP | EPOLLERR | EPOLLHUP))
                {

                }
            }
        }else if(nfds == 0){
            //描述符并不存在就绪
            continue;
        }else{
            if(errno == EINTR)
            {
                continue;
            }
            //出现错误情况
            LOG_TRACE(LOG_ERROR,false,"CEvent::eventLoop","epoll_wait failed");
        }

    }

}



bool CThreadSocket::addEvent(int fd,uint32_t flags)
{
    struct epoll_event event;
    bzero(&event,sizeof(event));
    event.data.fd = fd;
    event.events = (flags);



    int res = epoll_ctl(eventfd,EPOLL_CTL_ADD,fd,&event);
    if(res == -1)
    {
        LOG_TRACE(LOG_ERROR,false,"CUnixOs::eventAdd","add event error");
        return  false;
    }else{
        return true;
    }
}

bool CThreadSocket::deleteEvent(int fd)
{
    struct epoll_event event;
    bzero(&event,sizeof(event));

    epoll_ctl(eventfd,EPOLL_CTL_DEL,fd,NULL);
}

bool CThreadSocket::reconnect(int fd)
{
    //删除事件
    deleteEvent(fd);
    //断线进行重新链接
    socketHandle->reconnect();
    //加入事件循环
    addEvent(fd,EPOLLET|EPOLLIN|EPOLLERR|EPOLLHUP);
}

ssize_t CThreadSocket::sendData(int fd,void* vptr,size_t n)
{
    bool res;
    send:
    res = socketHandle->send(fd,vptr,n);

    if(res == false)
    {
        if(errno == EPIPE and errno == EBADF)
        {
            LOG_TRACE(LOG_ERROR,false,"CThreadSocket::sendData","send msg failed,write error.socket close");
            this->reconnect(fd);
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