//
// Created by zhanglei on 19-8-16.
//

#include "include/MainService.h"
using namespace service;

CEpoll::CEpoll() {
    mainLoop = EVENT_STOP;
    bzero(eventFunctionHandle, sizeof(eventFunctionHandle));
}

//创建事件
bool CEpoll::createEvent(int size) {

    if(mainLoop == EVENT_START)
    {
        LOG_TRACE(LOG_ERROR,false,"CUnixOs::createEvent","Event loop has been running");
        return  false;
    }

    epollFd = epoll_create(size);
    if(epollFd == -1)
    {
        LOG_TRACE(LOG_ERROR,false,"CUnixOs::createEvent","epoll_create failed");
        return  false;
    }

    eventSize = size;
    bzero(eventCollect,sizeof(struct epoll_event));
    return true;
}

bool CEpoll::hookAdd(int flag,eventHandle handle)
{
    if(flag > EPOLL_EVENTS_MAX){
        LOG_TRACE(LOG_ERROR,false,"CUnixOs::eventAdd","hookAdd failed");
        return  false;
    }
    eventFunctionHandle[flag] = handle;
    return  true;
}

bool CEpoll::eventAdd(int fd,uint32_t flags) {
    struct epoll_event event;
    bzero(&event,sizeof(event));
    event.data.fd = fd;
    event.events = (flags);

    int res = epoll_ctl(epollFd,EPOLL_CTL_ADD,fd,&event);
    if(res == -1)
    {
        LOG_TRACE(LOG_ERROR,false,"CEvent::eventAdd","CEvent->eventAdd;");
        return  false;
    }else{
        return true;
    }
}

uint32_t CEpoll::selectEventType(uint32_t flags)
{
    if(flags == CEVENT_READ)
    {
        flags = (EPOLLIN|EPOLLET);
    }

    return  flags;
}

bool CEpoll::eventUpdate(int fd,uint32_t flags) {
    struct epoll_event event;
    bzero(&event,sizeof(event));
    event.events = flags;
    event.data.fd = fd;

    epoll_ctl(epollFd,EPOLL_CTL_MOD,fd,&event);
}

bool CEpoll::eventDelete(int fd) {
    struct epoll_event event;
    bzero(&event,sizeof(event));

    epoll_ctl(epollFd,EPOLL_CTL_DEL,fd,NULL);
}



void CEpoll::stopLoop()
{
    mainLoop = EVENT_STOP;
}



//时间循环
void CEpoll::eventLoop(void* ptr) {
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
        nfds = epoll_wait(epollFd,eventCollect,eventSize,-1);

        //返回准备就绪的描述符
        if(nfds>0)
        {
            for(i=0;i<nfds;i++)
            {

                //触发可读事件
                if(eventCollect[i].events&EPOLLIN)
                {
                    if(eventFunctionHandle[CEVENT_READ]) {
                        eventFunctionHandle[CEVENT_READ](eventCollect[i],ptr);
                    }
                }else if(eventCollect[i].events & (EPOLLRDHUP | EPOLLERR | EPOLLHUP))
                {

                    if(eventFunctionHandle[CEVENT_ERROR]) {
                        eventFunctionHandle[CEVENT_ERROR](eventCollect[i],ptr);
                    }
                }else if(eventCollect[i].events & EPOLLOUT)
                {
                    if(eventFunctionHandle[CEVENT_WRITE]) {
                        eventFunctionHandle[CEVENT_WRITE](eventCollect[i],ptr);
                    }
                }
                usleep(20);
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
            LOG_TRACE(LOG_ERROR,false,"CEvent::eventLoop",":epoll_wait failed");
        }

    }
}

CEpoll::~CEpoll()
{
}
