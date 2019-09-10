//
// Created by zhanglei on 19-8-16.
//

#ifndef LOGSENTRY_CPOLL_H
#define LOGSENTRY_CPOLL_H

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

#ifndef FOPEN_MAX
#define  128
#endif

#include <list>
//typedef bool (*eventHandle)(struct epoll_event events);
typedef bool (*eventHandle)(struct pollfd events,void* ptr);

namespace service {
    class CEvent {

    public:
        CEvent();
        ~CEvent();
        bool createEvent(size_t size);
        bool eventAdd(int fd,uint32_t flags,eventHandle handle);
        bool eventUpdate(int fd,uint32_t flags,eventHandle handle);
        bool eventDelete(int fd);
        short selectEventType(uint32_t flags);
        void eventLoop(void* ptr);
        void stopLoop();
        struct pollfd clientCollect[FOPEN_MAX];
        size_t eventSize;
        list<int> collect;

    private:
        int epollFd;
        short mainLoop;
        int nfds;
        int pollSize;
        eventHandle eventFunctionHandle[EPOLL_EVENTS_MAX];
    };
}