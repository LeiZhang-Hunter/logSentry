//
// Created by zhanglei on 19-8-16.
//

#ifndef LOGSENTRY_CEPOLL_H
#define LOGSENTRY_CEPOLL_H

#endif //LOGSENTRY_CEVENT_H
#ifdef _SYS_EPOLL_H
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

typedef bool (*eventHandle)(struct epoll_event events,void* ptr);

namespace service {
    class CEpoll {

    public:
        CEpoll();
        ~CEpoll();
        bool createEvent(int size);
        bool eventAdd(int fd,uint32_t flags);
        bool eventUpdate(int fd,uint32_t flags);
        bool eventDelete(int fd);
        uint32_t selectEventType(uint32_t flags);
        void eventLoop(void* ptr);
        void stopLoop();
        eventHandle eventFunctionHandle[EPOLL_EVENTS_MAX];
        int eventSize;
        bool hookAdd(int flag,eventHandle handle);

    private:
        int epollFd;
        short mainLoop;
        int nfds;
        int pollSize;
        struct epoll_event*eventCollect;
    };
}
#endif