//
// Created by zhanglei on 19-8-16.
//

#ifndef LOGSENTRY_CEVENT_H
#define LOGSENTRY_CEVENT_H

#endif //LOGSENTRY_CEVENT_H

enum{
    EVENT_START = 1,
    EVENT_STOP = 0
};

//在epoll里最大的种类是32
#define EPOLL_EVENTS_MAX 32

typedef bool (*eventHandle)();

namespace service {
    class CEvent {

    public:
        CEvent(int size);
        bool createEvent();
        bool eventAdd(int fd,uint32_t flags,eventHandle handle);
        bool eventUpdate(int fd,uint32_t flags,eventHandle handle);
        bool eventDelete(int fd,uint32_t flags);
        void eventLoop();

    private:
        int epollFd;
        short mainLoop;
        int nfds;
        int pollSize;
        struct epoll_event*eventCollect;
    };
}