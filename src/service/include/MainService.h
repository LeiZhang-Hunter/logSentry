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
#include <sys/wait.h>
//#include <sys/epoll.h>
#include <sys/poll.h>
#include <sys/time.h>
#include <sys/resource.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <limits.h>

//自己的组件库
#ifndef LOGSENTRY_CSINGLEINSTANCE_H
#include "CSingleInstance.h"
#endif

#ifndef LOGSENTRY_CMUTEXLOCK_H
#include "CLock.h"
#endif

#ifndef LOGSENTRY_CSERVICELOG_H
#include "CServiceLog.h"
#endif


#ifndef SOCKETSENTRY_CTHREAD_H
#include "CThread.h"
#endif

#ifndef LOGSENTRY_CSOCKET_H
#include "CSocket.h"
#endif

//#ifndef LOGSENTRY_CEVENT_H
//#include "CEvent.h"
//#endif

#ifndef LOGSENTRY_CPOLL_H
#include "CPoll.h"
#endif

#ifndef LOGSENTRY_CTHREADSOCKET_H
#include "CThreadSocket.h"
#endif

#ifndef LOGSENTRY_CINIFILECONFIG_H
#include "CIniFileConfig.h"
#endif

#ifndef LOGSENTRY_CPROCESS_H
#include "CProcess.h"
#endif



#ifndef LOGSENTRY_CPROCESSFACTORY_H
#include "CProcessFactory.h"
#endif

#ifndef LOGSENTRY_CSIGNAL_H
#include "CSignal.h"
#endif


extern CServiceLog* logInstance;

#define LOG_TRACE(logLevel,isSucess,name,msg) logInstance->addLog(name,msg,__FILE__,__LINE__);

#ifndef LOGSENTRY_CUNIXOS_H
#include "CUnixOs.h"
#endif

enum {
    LOCK_PROCESS_IS_SHARED = 1,
    LOCK_PROCESS_NO_SHARED = 0,
    READ_LOCK = 1,
    WRITE_LOCK = 0
};
