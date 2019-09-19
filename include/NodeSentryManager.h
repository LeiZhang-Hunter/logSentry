//
// Created by zhanglei on 19-8-21.
//

#ifndef LOGSENTRY_FILEMONITORMANAGER_H
#define LOGSENTRY_FILEMONITORMANAGER_H

#endif //LOGSENTRY_FILEMONITORMANAGER_H


namespace app {
    class NodeSentryManager : public CProcessFactory {
    public:
        bool start();

        bool setConfig(map<string, map<string,string>> config);

        void onMonitor(pid_t, int);

        bool stopMonitor();
        map<pid_t, NodeSentry *> processPool;

    private:
        map<string, map<string,string>> monitorConfig;

    };
}

