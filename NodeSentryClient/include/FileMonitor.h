//
// Created by zhanglei on 19-8-16.
//


#ifndef LOGSENTRY_FILEMONITOR_H
#define LOGSENTRY_FILEMONITOR_H

#endif //LOGSENTRY_FILEMONITOR_H



namespace app {

    typedef struct file_data {
        int fild_fd;
        size_t begin;
        ssize_t offset;
    } file_read;


    class FileMonitor : public CProcess {
    public:
        FileMonitor();
//        ~FileMonitor();
        bool setNotifyPath(const char* path);
        bool setFileName(const char* file_name);
        string getFileName();
        string getNotifyPath();
        bool setWorkerNumber(int number);
        int getWorkerNumber();

        void start();
        void run() final;

#ifdef _SYS_EPOLL_H
        static bool onModify(struct epoll_event,void* ptr);
#else
        static bool onModify(struct pollfd,void* ptr);
#endif

#ifdef _SYS_EPOLL_H
        static bool onPipeWrite(struct epoll_event eventData,void* ptr);
#else
        static bool onPipeWrite(struct pollfd eventData,void* ptr);
#endif
        static void onStop(int sig);

    private:
        string monitorPath;
        int workerNumber;
        string fileName;
        CEvent* eventInstance;
        map<int,FileMonitorWorker*>temp_pool;

    };

}