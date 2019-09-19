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

#ifndef LOGSENTRY_FILEMONITOR_H
#include "FileMonitor.h"
#endif


#ifndef LOGSENTRY_NODESENTRY_H
#include "NodeSentry.h"
#endif

#ifndef LOGSENTRY_FILEMONITORMANAGER_H
#include "NodeSentryManager.h"
#endif




typedef struct file_data{
    size_t begin;
    ssize_t offset;
}file_read;

//监控节点
typedef struct _monitor_node{
    //路径名字
    char path[PATH_MAX];
    //监控的文件名字
    int inotify_fd;
    //文件的偏移量
    ssize_t file_offset;
    //描述符的fd
    int file_fd;
    //管道地址
    int(*pipe_collect)[2];
    //起始长度
    ssize_t begin_length;
    //工作的线程数目
    int workerNumberCount;
    //发送次数
    int send_number;
}monitor_node;

extern monitor_node file_node;


//监控文件的集合
//map<string,monitor_node>monitorCollect;
extern app::Config* config_instance;
extern app::NodeSentryManager* manager;
extern CSignal* sig_handle;
extern CUnixOs* os;
using namespace app;