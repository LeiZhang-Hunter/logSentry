//
// Created by zhanglei on 19-10-20.
//

#ifndef LOGSENTRY_DIRNODE_H
#define LOGSENTRY_DIRNODE_H

#endif //LOGSENTRY_DIRNODE_H

namespace app {

    typedef struct _file_dir_data{
        int file_fd;
        char name[NAME_MAX];
        ssize_t begin;
        ssize_t offset;
    }file_dir_data;

    class DirNode {
    public:
        DirNode() {
            bzero(&monitor_node, sizeof(monitor_node));
        }

        //监控节点
        struct _monitor_node {
            //路径名字
            char path[PATH_MAX];
            //监控的文件名字
            int inotify_fd;
            //文件的偏移量
            //描述符的fd
            DIR *monitor_dir;
            int wd;
            //管道地址
            int(*pipe_collect)[2];
            //发送次数
            int send_number;
        } monitor_node;

        bool initNode(int pipeNumber, const char *path);
        bool deleteMonitor();
        bool addMonitor();

        bool deleteFileToPool(const char* name);
        bool addFileToPool(const char* name);
    private:
        //目录的文件池
        map<const char*,file_dir_data>fileDirPool;
        uint32_t monitorFlags = IN_MODIFY|IN_DELETE_SELF|IN_DELETE|IN_MOVE_SELF|IN_IGNORED|IN_ATTRIB|IN_CREATE;

    };
}