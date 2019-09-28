//
// Created by zhanglei on 19-8-14.
//

#ifndef LOGSENTRY_CSERVICELOG_H
#define LOGSENTRY_CSERVICELOG_H

#endif //LOGSENTRY_SERVICELOG_H

enum {
    LOG_NOTICE = 0,
    LOG_WARING = 1,
    LOG_ERROR = 2,
    LOG_SUCESS = 3,
};
namespace service {
    class CServiceLog {
    public:
        CServiceLog(const char *dir);

        bool addLog(const char *name, const char *log, const char *file, int file_number);

        ~CServiceLog();

    private:
        CMutexLock *logLock;
        int logFd;

    };
}