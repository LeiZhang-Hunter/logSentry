//
// Created by zhanglei on 19-8-16.
//
#include "include/MainService.h"


bool CSignal::setSignalHandle(int signo, __sighandler_t sighandler_fun)
{
    struct sigaction act, act_g;

    act.sa_handler = sighandler_fun;

    act.sa_flags = 0;
    if (::sigaction(signo, &act, &act_g) < 0)
    {
        return true;
    }
    return false;
}



