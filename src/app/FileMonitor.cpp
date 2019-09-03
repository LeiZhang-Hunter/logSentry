//
// Created by zhanglei on 19-8-16.
//

#include "../../include/Common.h"
using namespace app;


FileMonitor::FileMonitor() {

}

void FileMonitor::start() {
    //创建worker
//    int res = this->createProcess();
//    if(res != 0)
//    {
//        LOG_TRACE(LOG_ERROR,false,"FileMonitor::start","create process error,errcode:"<<errno<<";errmsg:"<<strerror(errno)<<"line"<<__LINE__<<"\n");
//        return;
//    }
    int res;
    int wd;
    bool result;
    int thread_number;
    int pipe[2];

    bzero(&file_node,sizeof(file_node));

    //打开文件
    file_node.file_fd = open(monitorPath.c_str(),O_RDWR|O_CREAT,S_IRWXU);

    if(file_node.file_fd == -1)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","open fd error");
        return;
    }

    //初始化文件node节点
    strcpy(file_node.path,monitorPath.c_str());



    file_node.inotify_fd = inotify_init();

    if(file_node.inotify_fd < 0)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","create process error");
        return;
    }

    //监控文件内容修改以及元数据变动
    wd = inotify_add_watch(file_node.inotify_fd,file_node.path,IN_MODIFY|IN_ATTRIB|IN_MOVE_SELF);
    if(wd == -1)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","create inotify_add_watch error");
        return;
    }

    CEvent* eventInstance = CSingleInstance<CEvent>::getInstance();
    result = eventInstance->createEvent(512);
    if(!result)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","CEvent::createEvent error");
        return;
    }
    eventInstance->eventAdd(file_node.inotify_fd,CEVENT_READ,onModify);

    if(workerNumber<1)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","workerNumber  error");
        return;
    }

    Config* instance = CSingleInstance<Config>::getInstance();
    map<string,map<string,string>>mContent = instance->getConfig();

    //开始创建socket线程用来做读取后的数据收发
    printf("workerNumber:%d\n",workerNumber);
    file_node.pipe_collect = (int(*)[2])calloc((size_t)workerNumber,sizeof(pipe));
    file_node.workerNumberCount = workerNumber;
    for(thread_number=0;thread_number<workerNumber;thread_number++)
    {

        res = socketpair(AF_UNIX,SOCK_DGRAM,0,file_node.pipe_collect[thread_number]);
        if(res == -1)
        {
            LOG_TRACE(LOG_ERROR,false,"FileMonitor::start","socketpair  failed");
            continue;
        }
        CThreadSocket* socket_worker = new FileMonitorWorker(mContent["server"],file_node.pipe_collect[thread_number][0]);
        //启动线程
        socket_worker->Start();
    }
//    file_node.begin_length = buf.st_size;
    eventInstance->eventLoop();
}

bool FileMonitor::setWorkerNumber(int number) {
    workerNumber = number;
}

bool FileMonitor::setNotifyPath(string path) {
    monitorPath = path;
}

//在这里编写逻辑
void FileMonitor::run() {

}

//文件发生变化的逻辑在这里写
bool FileMonitor::onModify(struct epoll_event eventData) {
    struct inotify_event* event;
    //获取到实例
    char buf[BUFSIZ];
    int i = 0;
    struct stat file_buffer;
    file_read file_data;
    ssize_t readLen;
    bzero(buf,BUFSIZ);
    ssize_t read_size;
    int pipe_number;
    int pipe;
    ssize_t write_size;
    int res;
    int wd;

    if(!file_node.pipe_collect)
    {
        LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","unixPipe is null");
        return  false;
    }


    read_size = read(eventData.data.fd,buf,BUFSIZ);
    if(read_size>0)
    {
        while(i<read_size)
        {
            event = (struct inotify_event*)&buf[i];

            if(event->len) {
                printf("name=%s\n", event->name);
            }
            bzero(&file_buffer, sizeof(file_buffer));
//            LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","mask:"<<event->mask);
            //如果说文件发生了修改事件
            if(event->mask & IN_MODIFY) {
                //读取变化之后的文件大小

                res = fstat(file_node.file_fd, &file_buffer);
                if (res == -1) {
                    LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","fstat fd error");
                    return false;
                }
                LOG_TRACE(LOG_SUCESS,false,"FileMonitor::run","st_size:"<<file_buffer.st_size<<";begin_length:"<<file_node.begin_length);

                if(file_buffer.st_size>file_node.begin_length)
                {
                    readLen = file_buffer.st_size - file_node.begin_length;

                    bzero(&file_data, sizeof(file_data));

                    file_data.begin = (size_t)(file_buffer.st_size);
                    file_data.offset = readLen;

                    pipe_number = file_node.send_number%file_node.workerNumberCount;

                    if(file_node.pipe_collect)
                    {
                        pipe = *(file_node.pipe_collect[pipe_number]+1);
                        if(pipe > 0)
                        {
                            write_size = write(pipe,&file_data,sizeof(file_data));
                            printf("write_size:%ld;pipeFd:%d;send_number:%d;pipe_number:%d;file_node.workerNumberCount:%d\n",write_size,pipe,file_node.send_number,pipe_number,file_node.workerNumberCount);
                            if(write_size<=0)
                            {
                                LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","Write Pipe Fd Error");
                            }else{
                                file_node.send_number++;
                            }
                        }else{
                            LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","Get Pipe Fd Error");
                            return false;
                        }
                    }
                }

                file_node.begin_length = file_buffer.st_size;
            }else if(event->mask & IN_ATTRIB)
            {
                //检查文件是否被删除
                res = access(file_node.path,F_OK);

                //关闭掉旧的描述符
                if(file_node.file_fd > 0) {
                    close(file_node.file_fd);
                }

                //用新的文件句柄
                file_node.file_fd = open(file_node.path,O_RDWR|O_CREAT,S_IRWXU);
                if(file_node.file_fd == -1)
                {
                    LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","open fd error");
                    return false;
                }

                res = fstat(file_node.file_fd, &file_buffer);
                if (res == -1) {
                    LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","fstat fd error");
                    return false;
                }


                //更新文件起始读的位置
                file_node.begin_length = file_buffer.st_size;

                //添加文件到监视
                //监控文件内容修改以及元数据变动
                wd = inotify_add_watch(file_node.inotify_fd,file_node.path,IN_MODIFY|IN_ATTRIB);
                if(wd == -1)
                {
                    LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","create inotify_add_watch error");
                    return false;
                }
            }
            i+=(sizeof(struct inotify_event)+event->len);

        }

    }
}