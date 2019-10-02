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
        ~DirMonitorWorker();
    };
}