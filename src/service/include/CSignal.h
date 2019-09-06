//
// Created by zhanglei on 19-8-16.
//

#ifndef LOGSENTRY_CSIGNAL_H
#define LOGSENTRY_CSIGNAL_H

#endif //LOGSENTRY_CSIGNAL_H

namespace service {
    class CSignal {
    public:
        bool setSignalHandle(int signo, __sighandler_t sighandler_fun);
    };
}