//
// Created by zhanglei on 19-8-16.
//

#ifndef LOGSENTRY_CPOLL_H
#define LOGSENTRY_CPOLL_H

#endif //LOGSENTRY_CEVENT_H
#ifndef _SYS_EPOLL_H
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
#define FOPEN_MAX  128
#endif

#include <list>
//typedef bool (*eventHandle)(struct epoll_event events);
typedef bool (*eventHandle)(struct pollfd events,void* ptr);

namespace service {
    class CPoll {

    public:
        CPoll();
        ~CPoll();
        bool createEvent(size_t size);
        bool eventAdd(int fd,short flags,eventHandle handle);
        bool eventUpdate(int fd,uint32_t flags,eventHandle handle);
        bool eventDelete(int fd);
        short selectEventType(short flags);
        void eventLoop(void* ptr);
        void stopLoop();
        struct pollfd clientCollect[FOPEN_MAX];
        size_t eventSize;
        list<int> collect;
        eventHandle eventFunctionHandle[EPOLL_EVENTS_MAX];

    private:
        short mainLoop;
        int nfds;
    };
}
#endif