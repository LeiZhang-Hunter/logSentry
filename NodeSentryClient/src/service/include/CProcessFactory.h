//
// Created by zhanglei on 19-8-16.
//

#ifndef LOGSENTRY_CPROCESSFACTORY_H
#define LOGSENTRY_CPROCESSFACTORY_H

#endif //LOGSENTRY_CPROCESSFACTORY_H

enum {
    FACTORY_STOP = 0,
    FACTORY_RUN = 1,
};
namespace service{
    class CProcessFactory{
    public:
        ~CProcessFactory();
        virtual void onMonitor(pid_t,int){};
        virtual void stopMonitor(pid_t,int){};
        int startMonitor(pid_t monitor_process_id,int options);
        bool setPidFile(const char* pid_file);
        //停止工厂
        bool stopFactory();

    private:
        int monitorStatus;
        char pidFile[PATH_MAX];
        pid_t pid;

        int pidFd;
        //建立一个进程守护锁，防止进程被重复启动
        struct flock guard;
    };
}
