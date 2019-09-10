//
// Created by zhanglei on 19-9-10.
//

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
bool CEvent::createEvent(size_t size) {

    if(mainLoop == EVENT_START)
    {
        LOG_TRACE(LOG_ERROR,false,"CUnixOs::createEvent","Event loop has been running");
        return  false;
    }

    //创建一个事件集
    bzero(clientCollect,sizeof(clientCollect));
    eventSize = size;
    return  true;
}

bool CEvent::eventAdd(int fd,uint32_t flags,eventHandle handle) {
    struct pollfd client;
    int i;
    bzero(&client,sizeof(client));

    for(i=0;i<eventSize;i++)
    {
        clientCollect[i].fd = fd;
        clientCollect[i].events = selectEventType(flags);
    }


    if(flags > EPOLL_EVENTS_MAX)
    {
        LOG_TRACE(LOG_ERROR,false,"CUnixOs::eventAdd","eventAdd failed");
        return  false;
    }
    eventFunctionHandle[flags] = handle;
    //加入到列中
    collect.push_back(fd);
    return true;
}

short CEvent::selectEventType(uint32_t flags)
{
    if(flags == CEVENT_READ)
    {
        flags = POLLIN;
    }

    return  flags;
}

bool CEvent::eventUpdate(int fd,uint32_t flags,eventHandle handle) {
//    struct epoll_event event;
//    bzero(&event,sizeof(event));
//    event.events = flags;
//    event.data.ptr = (void*)handle;
//
//    epoll_ctl(epollFd,EPOLL_CTL_MOD,fd,&event);
}

bool CEvent::eventDelete(int fd) {
//    struct epoll_event event;
//    bzero(&event,sizeof(event));
//
//    epoll_ctl(epollFd,EPOLL_CTL_DEL,fd,NULL);
}

void CEvent::stopLoop()
{
    mainLoop = EVENT_STOP;
}



//时间循环
void CEvent::eventLoop(void* ptr) {
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
        nfds = poll(clientCollect,eventSize+1,-1);

        //返回准备就绪的描述符
        if(nfds>0)
        {
            for(i=0;i<nfds;i++)
            {

                //触发可读事件
                if(clientCollect[i].events&POLLIN)
                {
                    if(eventFunctionHandle[CEVENT_READ]) {
                        eventFunctionHandle[CEVENT_READ](clientCollect[i],ptr);
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
            LOG_TRACE(LOG_ERROR,false,"CEvent::eventLoop",":epoll_wait failed");
        }

    }
}

CEvent::~CEvent()
{
        free(clientCollect);
}