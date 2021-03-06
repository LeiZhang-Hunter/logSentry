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
#include <vector>
#ifdef linux
#include <sys/epoll.h>
#else
#include <sys/poll.h>
#endif
#include <ctime>
#include <sys/resource.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <climits>
#include <sys/stat.h>

#ifndef LOGSENTRY_CSTRINGBUFFER_H
#include "CStringBuffer.h"
#endif

#ifndef LOGSENTRY_CCURL_H
#include "CCurl.h"
#endif

//自己的组件库
#ifndef LOGSENTRY_CSINGLEINSTANCE_H
#include "CSingleInstance.h"
#endif

#ifndef LOGSENTRY_CMUTEXLOCK_H
#include "CLock.h"
#endif

#ifndef LOGSENTRY_CSIGNAL_H
#include "CSignal.h"
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



#ifdef _SYS_EPOLL_H
#ifndef LOGSENTRY_CEVENT_H
#include "CEpoll.h"
#endif
#else
#ifndef LOGSENTRY_CPOLL_H
#include "CPoll.h"
#endif
#endif

#include "CEvent.h"

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



#ifndef LOGSENTRY_CJSON_H
#include "CJson.h"
#endif

#define DECLARE_LOG CServiceLog* logInstance;


extern service::CServiceLog* logInstance;

#define LOG_TRACE(logLevel,isSucess,name,msg) \
    CStringBuffer strBuffer("");\
    strBuffer<<msg;\
logInstance->addLog(name,strBuffer.getBuffer().c_str(),__FILE__,__LINE__);

#ifndef LOGSENTRY_CUNIXOS_H
#include "CUnixOs.h"
#endif


