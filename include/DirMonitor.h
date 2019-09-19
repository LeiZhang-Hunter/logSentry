//
// Created by zhanglei on 19-9-19.
//

#ifndef LOGSENTRY_DIRMONITOR_H
#define LOGSENTRY_DIRMONITOR_H

#endif //LOGSENTRY_DIRMONITOR_H

class DirMonitor : public CProcess
{
public:
    bool setNotifyPath(const char* path);
    bool setFileName(const char* file_name);
    string getFileName();
    string getNotifyPath();
    bool setWorkerNumber(int number);
    int getWorkerNumber();

    //开始
    void start();
    //运行
    void run() final;

private:
    string monitorPath;
    int workerNumber;
    string fileName;
    CEvent* eventInstance;
    int inotify_fd;
    int inotify_wd;
};