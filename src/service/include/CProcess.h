//
// Created by zhanglei on 19-8-16.
//

#ifndef LOGSENTRY_CPROCESS_H
#define LOGSENTRY_CPROCESS_H

#endif //LOGSENTRY_CPROCESS_H

enum {
    PROCESS_RUN = 1,
    PROCESS_STOP = 0
};

namespace service {
    class CProcess {
    public:
        CProcess();
        pid_t getPid();
        virtual void run(){};
        int createDaemon();
        int createProcess();
        void waitProcess();
    private:
        int status;
        pid_t pid;
    };
}