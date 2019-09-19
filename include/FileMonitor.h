//
// Created by zhanglei on 19-8-16.
//


#ifndef LOGSENTRY_FILEMONITOR_H
#define LOGSENTRY_FILEMONITOR_H

#endif //LOGSENTRY_FILEMONITOR_H

typedef struct file_data{
    size_t begin;
    ssize_t offset;
}file_read;

//监控节点
typedef struct _monitor_node{
    //路径名字
    char path[PATH_MAX];
    //监控的文件名字
    int inotify_fd;
    //文件的偏移量
    ssize_t file_offset;
    //描述符的fd
    int file_fd;
    //管道地址
    int(*pipe_collect)[2];
    //起始长度
    ssize_t begin_length;
    //工作的线程数目
    int workerNumberCount;
    //发送次数
    int send_number;
}monitor_node;

extern monitor_node file_node;

namespace app {
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
        int wd;

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
    };

}