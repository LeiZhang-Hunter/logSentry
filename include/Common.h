//
// Created by zhanglei on 19-8-16.
//

#ifndef LOGSENTRY_COMMON_H
#define LOGSENTRY_COMMON_H

#endif //LOGSENTRY_COMMON_H

#include "../src/service/include/MainService.h"
#include <sys/inotify.h>
#include <sys/stat.h>
using namespace std;
using namespace service;
#ifndef SOCKETSENTRY_CONFIG_H
#include "Config.h"
#endif

#ifndef LOGSENTRY_MAINCENTER_H
#include "MainCenter.h"
#endif

#ifndef LOGSENTRY_FILEMONITOR_H
#include "FileMonitor.h"
#endif

#ifndef LOGSENTRY_FILEMONITORMANAGER_H
#include "FileMonitorManager.h"
#endif

#ifndef LOGSENTRY_FILEMONITORWORKER_H
#include "FileMonitorWorker.h"
#endif
