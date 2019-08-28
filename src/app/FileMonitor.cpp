//
// Created by zhanglei on 19-8-16.
//

#include "../../include/Common.h"

FileMonitor::FileMonitor() {

}

void FileMonitor::start() {
    //创建worker
    int res = this->createProcess();
    if(res != 0)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::start","create process error,errcode:"<<errno<<";errmsg:"<<strerror(errno)<<"line"<<__LINE__<<"\n");
    }
}

bool FileMonitor::setNotifyPath(string path) {
    monitorPath = path;
}

//在这里编写逻辑
void FileMonitor::run() {
    int fileNode = inotify_init();

    if(fileNode < 0)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","create process error,errcode:"<<errno<<";errmsg:"<<strerror(errno)<<"line"<<__LINE__<<"\n");
    }

    inotify_add_watch(fileNode,monitorPath.c_str(),IN_ATTRIB);
}