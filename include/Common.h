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

typedef struct file_data{
    size_t begin;
    off_t offset;
}file_read;

//监控节点
typedef struct _monitor_node{
    char path[PATH_MAX];
    int inotify_fd;
    ssize_t file_offset;
    int file_fd;
    int(*pipe_collect)[2];
    ssize_t begin_length;
    int workerNumberCount;
}monitor_node;

extern monitor_node file_node;


//监控文件的集合
//map<string,monitor_node>monitorCollect;


