//
// Created by zhanglei on 19-8-16.
//


#ifndef LOGSENTRY_FILEMONITOR_H
#define LOGSENTRY_FILEMONITOR_H

#endif //LOGSENTRY_FILEMONITOR_H

namespace app {
    class FileMonitor : public CProcess {
    public:
        FileMonitor();
        bool setNotifyPath(string path);
        void start();
        void run() final;
        bool setWorkerNumber(int number);
        static bool onModify(struct epoll_event);
        static void onStop(int sig);

    private:
        string monitorPath;
        int workerNumber;
        CEvent* eventInstance;
    };

}