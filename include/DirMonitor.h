//
// Created by zhanglei on 19-9-19.
//

#ifndef LOGSENTRY_DIRMONITOR_H
#define LOGSENTRY_DIRMONITOR_H

#endif //LOGSENTRY_DIRMONITOR_H

#include <dirent.h>

static map<string,int>fileDirPool;

namespace app {
    class DirMonitor : public CProcess {
    public:
        DirMonitor();

        bool setNotifyPath(const char *path);

        bool setFileName(const char *file_name);

        string getFileName();

        string getNotifyPath();

        bool setWorkerNumber(int number);

        int getWorkerNumber();

        static bool onChange(struct epoll_event, void *ptr);

        //开始
        void start();

        //运行
        void run() final;

    private:
        string monitorPath;
        int workerNumber;
        string fileName;
        CEvent *eventInstance;
        int inotify_fd;
        int inotify_wd;
        DIR* dirHandle;
        struct dirent *dirEntry;
    };
}