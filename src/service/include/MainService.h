//
// Created by zhanglei on 19-8-6.
//

#ifndef SOCKETSENTRY_MAINSERVICE_H
#define SOCKETSENTRY_MAINSERVICE_H

#endif //SOCKETSENTRY_MAINSERVICE_H

//内核库
#include <iostream>
#include <string>
#include <map>
#include <unistd.h>
#include <pthread.h>
#include <string.h>
#include <fcntl.h>
#include <stdio.h>
#include <signal.h>

//自己的组件库
#ifndef LOGSENTRY_SINGLEINSTANCE_H
#include "SingleInstance.h"
#endif
#ifndef LOGSENTRY_SERVICELOG_H
#include "ServiceLog.h"
#endif


#ifndef SOCKETSENTRY_CTHREAD_H
#include "CThread.h"
#endif
#ifndef LOGSENTRY_CMUTEXLOCK_H
#include "CMutexLock.h"
#endif
#ifndef LOGSENTRY_CSOCKET_H
#include "CSocket.h"
#endif
#ifndef LOGSENTRY_CINIFILECONFIG_H
#include "CIniFileConfig.h"
#endif

#ifndef LOGSENTRY_CPROCESS_H
#include "CProcess.h"
#endif

#define LOG_TRACE(logLevel,isSucess,Name,msg){}

enum {
    LOCK_PROCESS_IS_SHARED = 1,
    LOCK_PROCESS_NO_SHARED = 0,
    READ_LOCK = 1,
    WRITE_LOCK = 0
};
