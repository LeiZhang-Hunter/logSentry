//
// Created by zhanglei on 19-8-16.
//

#ifndef LOGSENTRY_COMMON_H
#define LOGSENTRY_COMMON_H

#endif //LOGSENTRY_COMMON_H

#include "../src/service/include/MainService.h"
#include <sys/inotify.h>
#include <sys/stat.h>
#include <limits.h>
#include <sys/prctl.h>
#include <malloc.h>
#include "jsoncpp/json/json.h"

using namespace std;
using namespace service;
#ifndef SOCKETSENTRY_CONFIG_H
#include "Config.h"
#endif

#ifndef LOGSENTRY_MAINCENTER_H
#include "MainCenter.h"
#endif

#ifndef LOGSENTRY_PROTOBUF_H
#include "ProtoBuf.h"
#endif

#ifndef LOGSENTRY_FILEMONITORWORKER_H
#include "FileMonitorWorker.h"
#endif

typedef struct _file_dir_data{
    int file_fd;
    char name[NAME_MAX];
    ssize_t begin;
    ssize_t offset;
}file_dir_data;

#ifndef LOGSENTRY_DIRMONITORWORKER_H
#include "DirMonitorWorker.h"
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