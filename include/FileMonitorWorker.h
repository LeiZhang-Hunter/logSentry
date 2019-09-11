//
// Created by zhanglei on 19-8-30.
//

#ifndef LOGSENTRY_FILEMONITORWORKER_H
#define LOGSENTRY_FILEMONITORWORKER_H

#endif //LOGSENTRY_FILEMONITORWORKER_H
namespace app {
    class FileMonitorWorker : public CThreadSocket {
    public:
        FileMonitorWorker(map<string,string> socketConfig,int pipe_fd);
        ~FileMonitorWorker();
        bool onCreate();
        bool onConnect() override;
        bool onClose() override;
#ifdef _SYS_EPOLL_H
        static bool onReceive(struct epoll_event event,void* ptr);
#else
        static bool onReceive(struct pollfd event,void* ptr);
#endif
        bool onClientRead(int fd,char* buf);
        struct protocolStruct{
            char path[PATH_MAX];
            char buf[BUFSIZ];
            char logName[NAME_MAX];
        };
        string filePath;
        string fileName;

    private:
        void onPipe(int fd,char* buf,size_t len);
        map<string,string>netConfig;
        int pipe;
        int fileFd;
        int client_fd;

    };
}