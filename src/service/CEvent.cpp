//
// Created by zhanglei on 19-8-16.
//

#include "include/MainService.h"
using namespace service;

CEvent::CEvent() {
    mainLoop = EVENT_STOP;
    bzero(eventFunctionHandle, sizeof(eventFunctionHandle));
}

//创建事件
bool CEvent::createEvent(int size) {

    if(mainLoop == EVENT_START)
    {
        LOG_TRACE(LOG_ERROR,false,"CUnixOs::createEvent","Event loop has been running"<<";in line:"<<__LINE__);
        return  false;
    }

    epollFd = epoll_create(size);
    if(epollFd == -1)
    {
        LOG_TRACE(LOG_ERROR,false,"CUnixOs::createEvent","epoll_create failed;errorcode:"<<errno<<";errormsg:"<<strerror(errno)<<";in line:"<<__LINE__);
        return  false;
    }

    //创建一个事件集
    eventCollect = (struct epoll_event*)calloc(2,sizeof(struct epoll_event));
    bzero(eventCollect,sizeof(struct epoll_event)*2);

    return  true;
}

bool CEvent::eventAdd(int fd,uint32_t flags,eventHandle handle) {
    struct epoll_event event;
    bzero(&event,sizeof(event));
    event.data.fd = fd;
    event.events = selectEventType(flags);

    if(flags > EPOLL_EVENTS_MAX)
    {
        LOG_TRACE(LOG_ERROR,false,"CUnixOs::eventAdd","eventAdd failed");
        return  false;
    }
    eventFunctionHandle[flags] = handle;
    int res = epoll_ctl(epollFd,EPOLL_CTL_ADD,fd,&event);
    if(res == -1)
    {
        LOG_TRACE(LOG_ERROR,false,"CEvent::eventAdd","CEvent->eventAdd;");
        return  false;
    }else{
        return true;
    }
}

uint32_t CEvent::selectEventType(uint32_t flags)
{
    if(flags == CEVENT_READ)
    {
        flags = (EPOLLIN|EPOLLET);
    }

    return  flags;
}

bool CEvent::eventUpdate(int fd,uint32_t flags,eventHandle handle) {
    struct epoll_event event;
    bzero(&event,sizeof(event));
    event.events = flags;
    event.data.ptr = (void*)handle;

    epoll_ctl(epollFd,EPOLL_CTL_MOD,fd,&event);
}

bool CEvent::eventDelete(int fd) {
    struct epoll_event event;
    bzero(&event,sizeof(event));

    epoll_ctl(epollFd,EPOLL_CTL_DEL,fd,NULL);
}




//时间循环
void CEvent::eventLoop() {
    //事件循环已经运行了
    if(mainLoop == EVENT_START)
    {
        LOG_TRACE(LOG_ERROR,false,"CUnixOs::eventLoop","eventLoop have running");
        return;
    }

    mainLoop = EVENT_START;
    int i;

    while(mainLoop)
    {
        nfds = epoll_wait(epollFd,eventCollect,512,-1);

        //返回准备就绪的描述符
        if(nfds>0)
        {
            for(i=0;i<nfds;i++)
            {

                //触发可读事件
                if(eventCollect[i].events&EPOLLIN)
                {
                    if(eventFunctionHandle[CEVENT_READ]) {
                        eventFunctionHandle[CEVENT_READ](eventCollect[i]);
                    }
                }else if(eventCollect[i].events & (EPOLLRDHUP | EPOLLERR | EPOLLHUP))
                {

                    if(eventFunctionHandle[CEVENT_ERROR]) {
                        eventFunctionHandle[CEVENT_ERROR](eventCollect[i]);
                    }
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
            LOG_TRACE(LOG_ERROR,false,"CEvent::eventLoop",__LINE__<<":epoll_wait failed");
        }

    }
}

CEvent::~CEvent()
{
    if(eventCollect)
        free(eventCollect);
}