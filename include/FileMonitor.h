//
// Created by zhanglei on 19-8-16.
//


#ifndef LOGSENTRY_FILEMONITOR_H
#define LOGSENTRY_FILEMONITOR_H

#endif //LOGSENTRY_FILEMONITOR_H

class FileMonitor :public CProcess{
public:
    FileMonitor();
    bool setNotifyPath(string path);
    void start();
    void run();
    static bool onModify(struct epoll_event);
    static int fileFd;

private:
    string monitorPath;
};

