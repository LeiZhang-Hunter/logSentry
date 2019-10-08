//
// Created by zhanglei on 19-9-19.
//

#ifndef LOGSENTRY_DIRMONITOR_H
#define LOGSENTRY_DIRMONITOR_H

#endif //LOGSENTRY_DIRMONITOR_H

#include <dirent.h>
#include <list>

static map<string,int>fileDirPool;

typedef struct _file_dir_data{
    size_t begin;
    ssize_t offset;
}file_dir_data;

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

        static bool onSend(struct epoll_event, void *ptr);

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
        int (*pipe_collect)[2];
        list<int> eventPool;
        int send_number=0;
    };
}