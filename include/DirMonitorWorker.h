//
// Created by root on 19-10-2.
//

#ifndef LOGSENTRY_DIRMONITORWORKER_H
#define LOGSENTRY_DIRMONITORWORKER_H

#endif //LOGSENTRY_DIRMONITORWORKER_H

namespace app {

    class DirMonitorWorker : public CThreadSocket{
    public:
        DirMonitorWorker(map<string,string> socketConfig,int pipe_fd);
        bool onCreate();
        bool onConnect() override;
        bool onClose() override;
        static bool onReceive(struct epoll_event event,void* ptr);
        void onPipe(int fd, file_dir_data *buf,size_t len);
        ~DirMonitorWorker() override;
        CJson jsonTool;//json压缩工具
        ProtoBufMsg protoTool;//协议压缩工具

        int pipe;
        int client_fd;
    private:
        map<string,string>netConfig;

    };
}