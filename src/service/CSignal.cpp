//
// Created by zhanglei on 19-8-16.
//
#include "MainService.h"

using namespace service;

bool CSignal::setSignalHandle(int signo, __sighandler_t sighandler_fun)
{
    struct sigaction act, act_g;

    sigemptyset(&act.sa_mask);
    act.sa_handler = sighandler_fun;

    act.sa_flags = 0;
    int res = ::sigaction(signo, &act, &act_g);
    if (res == 0)
    {
        return true;
    }
    return false;
}



