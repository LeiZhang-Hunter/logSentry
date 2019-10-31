//
// Created by zhanglei on 19-8-16.
//

#ifndef LOGSENTRY_COMMON_H
#define LOGSENTRY_COMMON_H

#endif //LOGSENTRY_COMMON_H

#include "MainService.h"
#include <sys/inotify.h>
#include <sys/stat.h>
#include <limits.h>
#include <sys/prctl.h>
#include <malloc.h>
#include <dirent.h>


using namespace std;
using namespace service;

#define SENTRY_VERSION 1
#define MAGIC 103

enum {
    JSON_PROTO,
    PROTOBUF_PROTO,
    XML_PROTO
};

#ifndef SOCKETSENTRY_CONFIG_H
#include "Config.h"
#endif

#ifndef LOGSENTRY_MAINCENTER_H
#include "MainCenter.h"
#endif

#ifndef LOGSENTRY_PROTOBUF_H
#include "ProtoBufMsg.h"
#endif

#ifndef LOGSENTRY_DIRNODE_H
#include "DirNode.h"
#endif

#ifndef LOGSENTRY_FILEMONITORWORKER_H
#include "FileMonitorWorker.h"
#endif

#ifndef LOGSENTRY_DIRMONITORWORKER_H
#include "DirMonitorWorker.h"
#endif

#ifndef LOGSENTRY_FILENODE_H
#include "FileNode.h"
#endif

#ifndef LOGSENTRY_FILEMONITOR_H
#include "FileMonitor.h"
#endif

#ifndef LOGSENTRY_DIRMONITOR_H
#include "DirMonitor.h"
#endif


#ifndef LOGSENTRY_NODESENTRY_H
#include "NodeSentry.h"
#endif

#ifndef LOGSENTRY_FILEMONITORMANAGER_H
#include "NodeSentryManager.h"
#endif


//一些全局的实例
extern app::Config* config_instance;
extern app::NodeSentryManager* manager;
extern CSignal* sig_handle;
extern CUnixOs* os;
using namespace app;