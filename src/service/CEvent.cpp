//
// Created by zhanglei on 19-8-16.
//

#include "include/MainService.h"
using namespace service;

CEvent::CEvent(int size) {
    mainLoop = EVENT_STOP;
    pollSize = size;
}

//创建事件
bool CEvent::createEvent() {

    if(mainLoop == EVENT_START)
    {
        LOG_TRACE(LOG_ERROR,false,"CUnixOs::createEvent","Event loop has been running"<<";in line:"<<__LINE__);
        return  false;
    }

    epollFd = epoll_create(pollSize);
    if(epollFd == -1)
    {
        LOG_TRACE(LOG_ERROR,false,"CUnixOs::createEvent","errorcode"<<errno<<";errormsg:"<<strerror(errno)<<";in line:"<<__LINE__);
        return  false;
    }

    //创建一个事件集
    eventCollect = (struct epoll_event*)calloc(512,sizeof(struct epoll_event));

    return  true;
}

bool CEvent::eventAdd(int fd,uint32_t flags,eventHandle handle) {
    struct epoll_event event;
    bzero(&event,sizeof(event));
    event.events = flags;
    event.data.ptr = (void*)handle;

    epoll_ctl(epollFd,EPOLL_CTL_ADD,fd,&event);
}

bool CEvent::eventUpdate(int fd,uint32_t flags,eventHandle handle) {
    struct epoll_event event;
    bzero(&event,sizeof(event));
    event.events = flags;
    event.data.ptr = (void*)handle;

    epoll_ctl(epollFd,EPOLL_CTL_MOD,fd,&event);
}

bool CEvent::eventDelete(int fd,uint32_t flags) {
    struct epoll_event event;
    bzero(&event,sizeof(event));
    event.events = flags;

    epoll_ctl(epollFd,EPOLL_CTL_DEL,fd,&event);
}




//时间循环
void CEvent::eventLoop() {
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
                ((eventHandle)(eventCollect[i].data.ptr))();
            }
        }else if(nfds == 0){
            //描述符并不存在就绪
            continue;
        }else{
            //出现错误情况
            LOG_TRACE(LOG_ERROR,false,"CEvent::eventLoop",__LINE__<<":epoll_wait failed,error msg:"<<strerror(errno));
        }

    }
}