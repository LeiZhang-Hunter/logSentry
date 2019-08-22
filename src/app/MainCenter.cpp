//
// Created by zhanglei on 19-8-16.
//
#include "../../include/Common.h"
using app::MainCenter;
using service::CSingleInstance;
using namespace std;
//运行逻辑

//执行逻辑
void MainCenter::start() {
    Config* instance = CSingleInstance<Config>::getInstance();
    map<string,map<string,string>>mContent = instance->getConfig();
    FileMonitorManager* manager = CSingleInstance<FileMonitorManager>::getInstance();
    if(!mContent["sentry_log_file"].empty())
    {
        //设置配置文件
        manager->setConfig(mContent["sentry_log_file"]);
        //启动管理者进程
        manager->start();
    }else{
        LOG_TRACE(LOG_ERROR,false,"MainCenter::run",__LINE__<<":The log option in the configuration file does not exist");
    }
}