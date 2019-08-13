//
// Created by zhanglei on 19-8-6.
//

#ifndef SOCKETSENTRY_MAINSERVICE_H
#define SOCKETSENTRY_MAINSERVICE_H

#endif //SOCKETSENTRY_MAINSERVICE_H

#include <iostream>

#include <unistd.h>

#include <pthread.h>

#include <string.h>

#ifndef LOGSENTRY_SINGLEINSTANCE_H
#include "SingleInstance.h"
#endif

#include "Config.h"

#ifndef SOCKETSENTRY_CTHREAD_H
#include "CThread.h"
#endif

#ifndef LOGSENTRY_CMUTEXLOCK_H
#include "CMutexLock.h"
#endif

#ifndef LOGSENTRY_CSOCKET_H
#include "CSocket.h"
#endif


enum {
    LOCK_PROCESS_IS_SHARED = 1,
    LOCK_PROCESS_NO_SHARED = 0,
    READ_LOCK = 1,
    WRITE_LOCK = 0
};
