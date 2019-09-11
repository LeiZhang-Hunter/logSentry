//
// Created by zhanglei on 19-8-16.
//

#ifndef LOGSENTRY_CEVENT_H
#define LOGSENTRY_CEVENT_H

#endif //LOGSENTRY_CEVENT_H



namespace service {
#ifdef _SYS_EPOLL_H
    class CEvent :public CEpoll{
#else
    class CEvent :public CPoll{
#endif
    };
}