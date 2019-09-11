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
        bool setFileName(string file_name);
        string getFileName();
        string getNotifyPath();

        void start();
        void run() final;
        bool setWorkerNumber(int number);
        int getWorkerNumber();
#ifdef _SYS_EPOLL_H
        static bool onModify(struct epoll_event,void* ptr);
#else
        static bool onModify(struct pollfd,void* ptr);
#endif
        static void onStop(int sig);

    private:
        string monitorPath;
        int workerNumber;
        string fileName;
        CEvent* eventInstance;
    };

}