//
// Created by zhanglei on 19-8-16.
//
#include "../../include/Common.h"
using app::MainCenter;
using service::CSingleInstance;
using namespace std;
//运行逻辑
CServiceLog* logInstance;

void MainCenter::sigHandle(int sig)
{
    switch (sig)
    {
        case SIGTERM:
            //进程管理者的实例
            map<pid_t,FileMonitor*>::iterator it;
            //进程管理者的实例
            FileMonitorManager* manager = CSingleInstance<FileMonitorManager>::getInstance();
            if(manager) {
                int i = 0;
                for (it = manager->monitorPool.begin(); it != manager->monitorPool.end(); it++) {
                    ::kill(it->second->getPid(), SIGTERM);
                }

                sleep(1);

                manager->stopFactory();
            }
            break;
    }
}

//执行逻辑
void MainCenter::start() {
    Config* instance = CSingleInstance<Config>::getInstance();
    //加入信号处理函数
    CSignal* sig_handle= CSingleInstance<CSignal>::getInstance();
    //进程管理者的实例
    FileMonitorManager* manager = CSingleInstance<FileMonitorManager>::getInstance();
    map<string,map<string,string>>mContent = instance->getConfig();

    logInstance = new CServiceLog(mContent["file_path"]["file_path"].c_str());



    //处理关闭事件关掉子进程
    sig_handle->setSignalHandle(SIGTERM,sigHandle);

    //忽略掉SIGPIPE防止信号挂掉
    sig_handle->setSignalHandle(SIGPIPE,SIG_IGN);


    if(!mContent["sentry_log_file"].empty())
    {
        //设置配置文件
        manager->setConfig(mContent["sentry_log_file"]);
        manager->setPidFile(mContent["sentry"]["pid_file"].c_str());
        //启动管理者进程
        manager->start();
    }else{
        LOG_TRACE(LOG_ERROR,false,"MainCenter::run","The log option in the configuration file does not exist");
    }

    //销毁掉实例防止内存泄露
    delete instance;
    delete sig_handle;
    delete manager;
}

