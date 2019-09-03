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
        LOG_TRACE(LOG_ERROR,false,"CThreadSocket::CThreadSocket","errorcode:"<<errno<<";errormsg:"<<strerror(errno)<<";in line:"<<__LINE__);
        return;
    }

    //创建一个事件集
    eventCollect = (struct epoll_event*)calloc(2,sizeof(struct epoll_event));
    bzero(eventCollect,sizeof(struct epoll_event)*2);
}

void CThreadSocket::Execute()
{
    int res;

    if(run == 1)
    {
        LOG_TRACE(LOG_ERROR,false,"CThreadSocket::Execute","socketHandle Client Has Running"<<";line:"<<__LINE__<<"\n");
        return;
    }


    this->onCreate();

    //进行连接
    res = socketHandle->connect();
    if(res == 0)
    {
        LOG_TRACE(LOG_ERROR,false,"CThreadSocket::Execute","socketHandle->connect error,errcode:"<<errno<<";errmsg:"<<strerror(errno)<<";line:"<<__LINE__<<"\n");
        return;
    }

    int nfds;
    int i;
    char buf[BUFSIZ];
    ssize_t read_size;

    run = 1;

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
                    read_size = recv(eventCollect[i].data.fd,buf, sizeof(buf),0);
                    if(read_size == 0)
                    {
                        //套接字关闭处理
                    }else if(read_size < 0)
                    {
                        if(errno == EINTR)
                        {
                            continue;
                        }
                    }else{
                        onReceive(eventCollect[i].data.fd,buf,sizeof(buf));
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
            LOG_TRACE(LOG_ERROR,false,"CEvent::eventLoop",__LINE__<<":epoll_wait failed,error msg:"<<strerror(errno));
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

CSocket* CThreadSocket::getSocketHandle()
{
    return socketHandle;
}

CThreadSocket::~CThreadSocket() {

    //释放掉socket句柄
    if(socketHandle)
        delete(socketHandle);
}