//
// Created by zhanglei on 19-8-16.
//

#ifndef LOGSENTRY_CPROCESS_H
#define LOGSENTRY_CPROCESS_H

#endif //LOGSENTRY_CPROCESS_H

enum {
    PROCESS_RUN = 1,

    MONITOR_STOP = 0,
};

namespace service {
    class CProcess {
    public:
        CProcess();
        pid_t getPid();
        virtual void run(){};
        int createDaemon();
        int createProcess();
        pid_t waitProcess(pid_t monitor_process_id,int options);

    private:
        int status;
        int monitorStatus;
        pid_t pid;
    };
}