//
// Created by zhanglei on 19-9-19.
//

#ifndef LOGSENTRY_NODESENTRY_H
#define LOGSENTRY_NODESENTRY_H

#endif //LOGSENTRY_NODESENTRY_H

enum {
    LOG_SENTRY,
    DIR_SENTRY,
    FLOW_SENTRY
};

namespace app {
    class NodeSentry{
    public:
        int sentryMode;
        bool setMode(int mode);
        bool setWorkerCount(int count);
        int getWorkerCount(int count);

        //启动程序载入配置文件
        template <typename T> void start(T config){
            switch(sentryMode) {
                case LOG_SENTRY: {
                    auto file_monitor = new FileMonitor();
                    file_monitor->setFileName(config->first.c_str());
                    file_monitor->setNotifyPath(config->second.c_str());
                    file_monitor->setWorkerNumber(worker_count);
                    file_monitor->start();
                    pid = file_monitor->getPid();
                    instance = file_monitor;
                }
                    break;
                case DIR_SENTRY: {
                    auto dir_monitor = new DirMonitor();
                    dir_monitor->setFileName(config->first.c_str());
                    dir_monitor->setNotifyPath(config->second.c_str());
                    dir_monitor->setWorkerNumber(worker_count);
                    dir_monitor->start();
                    pid = dir_monitor->getPid();
                    instance = dir_monitor;
                }
                    break;
                case FLOW_SENTRY:{

                }
                    break;
            }
        };

        //关闭程序
        template <typename typeInstance> typeInstance* getInstance(){
            return (typeInstance*)instance;
        };

        pid_t getPid();

        ~NodeSentry();
    private:
        int worker_count;
        pid_t pid;
        void* instance;
    };


}

