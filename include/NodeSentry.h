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
                case LOG_SENTRY:
                    auto monitor = new FileMonitor();
                    monitor->setFileName(config->first.c_str());
                    monitor->setNotifyPath(config->second.c_str());
                    monitor->setWorkerNumber(worker_count);
                    monitor->start();
                    pid = monitor->getPid();
                    instance = monitor;
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

