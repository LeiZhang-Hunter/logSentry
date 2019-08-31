//
// Created by zhanglei on 19-8-30.
//

#ifndef LOGSENTRY_FILEMONITORWORKER_H
#define LOGSENTRY_FILEMONITORWORKER_H

#endif //LOGSENTRY_FILEMONITORWORKER_H
namespace app {
    class FileMonitorWorker : public CThreadSocket {
    public:
        FileMonitorWorker(map<string,string> socketConfig);
        ~FileMonitorWorker();
        bool onCreate();
        bool onConnect() override;
        bool onClose() override;
        bool onReceive(int fd,char* buf) override;

    private:
        map<string,string>netConfig;
    };
}