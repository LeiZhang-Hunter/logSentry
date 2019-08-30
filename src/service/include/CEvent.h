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

enum {
    CEVENT_READ,
    CEVENT_WRITE,
    CEVENT_ERROR

};

//在epoll里最大的种类是32
#define EPOLL_EVENTS_MAX 32

typedef bool (*eventHandle)(struct epoll_event events);

namespace service {
    class CEvent {

    public:
        CEvent();
        bool createEvent(int size);
        bool eventAdd(int fd,uint32_t flags,eventHandle handle);
        bool eventUpdate(int fd,uint32_t flags,eventHandle handle);
        bool eventDelete(int fd);
        uint32_t selectEventType(uint32_t flags);
        void eventLoop();

    private:
        int epollFd;
        short mainLoop;
        int nfds;
        int pollSize;
        struct epoll_event*eventCollect;
        eventHandle eventFunctionHandle[EPOLL_EVENTS_MAX];
    };
}