//
// Created by zhanglei on 19-8-16.
//
#include "../../include/Common.h"
using app::MainCenter;
//声明全局变量
DECLARE_LOG
Config* config_instance;
CSignal* sig_handle;
FileMonitorManager* manager;

void MainCenter::sigHandle(int sig)
{
    switch (sig)
    {
        case SIGTERM:
            //进程管理者的实例
            map<pid_t,FileMonitor*>::iterator it;
            //进程管理者的实例
            if(manager) {
                int i = 0;
                for (it = manager->processPool.begin(); it != manager->processPool.end(); it++) {
                    ::kill(it->second->getPid(), SIGTERM);
                }

                sleep(1);

                manager->stopFactory();
            }
            break;
    }
}

bool MainCenter::init(string path)
{
    //解析命令行参数,获取配置文件路径
    config_instance = CSingleInstance<Config>::getInstance();

    if(!config_instance)
    {
        exit(-1);
    }

    config_instance->setPath(path);

    //如果说存在
    if(config_instance->getPath().c_str())
    {
        config_instance->loadConfig();
    }else{
        exit(-1);
    }

    //加入信号处理函数
    sig_handle= CSingleInstance<CSignal>::getInstance();
    //进程管理者的实例
    manager = CSingleInstance<FileMonitorManager>::getInstance();
}

//执行逻辑
void MainCenter::start() {

    map<string,map<string,string>>mContent = config_instance->getConfig();

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
}

bool MainCenter::destroy()
{
    delete config_instance;

    delete sig_handle;

    manager->stopMonitor();

    delete manager;

    delete logInstance;
}