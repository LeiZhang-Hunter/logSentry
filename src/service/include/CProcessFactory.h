//
// Created by zhanglei on 19-8-16.
//

#ifndef LOGSENTRY_CPROCESSFACTORY_H
#define LOGSENTRY_CPROCESSFACTORY_H

#endif //LOGSENTRY_CPROCESSFACTORY_H

#include <limits.h>
enum {
    PROCESS_STOP = 0,
    MONITOR_RUN = 1,
};
namespace service{
    class CProcessFactory{
    public:
        virtual void onMonitor(pid_t,int){};
        int startMonitor(pid_t monitor_process_id,int options);
        bool setPidFile(char* pid_file);

    private:
        int monitorStatus;
        char pidFile[PATH_MAX];
        int pid;
    };
}
