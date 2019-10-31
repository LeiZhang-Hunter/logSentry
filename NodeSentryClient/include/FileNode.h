//
// Created by zhanglei on 19-10-20.
//

#ifndef LOGSENTRY_FILENODE_H
#define LOGSENTRY_FILENODE_H

#endif //LOGSENTRY_FILENODE_H

/**
 * 这个类是不加锁的，用pread可以有效保证读文件的原子性,但是至于一些其他的修改读取操作不可以在多线程中使用，
 * 只在主线程中使用就可以
 * 这样会导致出现线程安全问题
 */

namespace app {
    class FileNode {

    public:
        FileNode()
        {
            bzero(&monitor_node,sizeof(monitor_node));
        }
        //监控节点
        struct _monitor_node {
            //路径名字
            char path[PATH_MAX];
            //监控的文件名字
            int inotify_fd;
            //文件的偏移量
            //描述符的fd
            int file_fd;
            int wd;
            //管道地址
            int(*pipe_collect)[2];
            //起始长度
            ssize_t begin_length;
            //工作的线程数目
            int workerNumberCount;
            //发送次数
            int send_number;
        } monitor_node;

        bool initNode(int pipeNumber,const char* path);

        bool deleteMonitor();

        bool reloadNode();

        bool addMonitor();

        bool setBeginLen(ssize_t len);

    private:
        int fileFlags = O_RDWR|O_CREAT;

        int filePriFlags = S_IRWXU;

        uint32_t monitorFlags = IN_MODIFY|IN_DELETE_SELF|IN_DELETE|IN_MOVE_SELF|IN_IGNORED|IN_ATTRIB;
        //        uint32_t monitorFlags = IN_ALL_EVENTS;

    };
}