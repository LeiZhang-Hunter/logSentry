//
// Created by zhanglei on 19-8-21.
//

#ifndef LOGSENTRY_FILEMONITORMANAGER_H
#define LOGSENTRY_FILEMONITORMANAGER_H

#endif //LOGSENTRY_FILEMONITORMANAGER_H



class FileMonitorManager : public CProcessFactory
{
public:
    bool start();

    bool setConfig(map<string,string>config);

    void onMonitor(pid_t,int);

private:
    map<string,string> monitorConfig;
    map<int,FileMonitor*> monitorPool;
};