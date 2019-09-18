//
// Created by zhanglei on 19-8-16.
//

#include "../../include/Common.h"


FileMonitor::FileMonitor() {

}

void FileMonitor::onStop(int sig)
{
    switch(sig)
    {
        case SIGTERM:
            CEvent* eventInstance = CSingleInstance<CEvent>::getInstance();
            //停止主事件循环
            eventInstance->stopLoop();
            //停止主重连事件
            break;
    }

}

void FileMonitor::start() {
    //创建worker
//    int res = this->createProcess();
//    if(res != 0)
//    {
//        LOG_TRACE(LOG_ERROR,false,"FileMonitor::start","create process error");
//        return;
//    }
    this->run();
}

bool FileMonitor::setFileName(const char* file_name)
{
    fileName = (file_name);
}

string FileMonitor::getFileName()
{
    return fileName;
}


bool FileMonitor::setWorkerNumber(int number) {
    workerNumber = number;
}

bool FileMonitor::setNotifyPath(const char* path) {
    monitorPath = path;
}

string FileMonitor::getNotifyPath(){
    return monitorPath;
}

int FileMonitor::getWorkerNumber()
{
    return workerNumber;
}

//在这里编写逻辑
void FileMonitor::run() {
    int res;
    int wd;
    bool result;
    int thread_number;
    int pipe[2];
    FileMonitorWorker* socket_worker;

#ifdef __linux__
    if(prctl(PR_SET_PDEATHSIG, SIGTERM) != 0)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","prctl set error");
    }
#endif

    eventInstance = CSingleInstance<CEvent>::getInstance();
    sig_handle->setSignalHandle(SIGTERM,onStop);

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

    result = eventInstance->createEvent(1+workerNumber);
    if(!result)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","CEvent::createEvent error");
        return;
    }


    eventInstance->hookAdd(CEVENT_READ,onModify);

    eventInstance->hookAdd(CEVENT_WRITE,onPipeWrite);

    eventInstance->eventAdd(file_node.inotify_fd,EPOLLIN|EPOLLET);

    if(workerNumber<1)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","workerNumber  error");
        return;
    }

    map<string,map<string,string>>mContent = config_instance->getConfig();


    //开始创建socket线程用来做读取后的数据收发
    file_node.pipe_collect = (int(*)[2])calloc((size_t)workerNumber,sizeof(pipe));
    file_node.workerNumberCount = workerNumber;
    for(thread_number=0;thread_number<workerNumber;thread_number++)
    {
        res = socketpair(AF_UNIX,SOCK_DGRAM,0,file_node.pipe_collect[thread_number]);
        if(res == -1)
        {
            LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","socketpair  failed");
            continue;
        }

        socket_worker = new FileMonitorWorker(mContent["server"],file_node.pipe_collect[thread_number][0]);

        socket_worker->filePath = monitorPath;

        socket_worker->fileName = fileName;

        //设置线程为守护线程
        socket_worker->SetDaemonize();
        //启动线程
        socket_worker->Start();
        eventInstance->eventAdd(file_node.pipe_collect[thread_number][1],EPOLLET|EPOLLIN);
    }

    struct stat buf;
    res = fstat(file_node.file_fd, &buf);
    if (res == -1) {
        LOG_TRACE(LOG_ERROR, false, "FileMonitor::run","fstat fd error");
        return;
    }
    file_node.begin_length = buf.st_size;
    eventInstance->eventLoop(this);
    delete(eventInstance);
}


//文件发生变化的逻辑在这里写
#ifdef _SYS_EPOLL_H
bool FileMonitor::onModify(struct epoll_event eventData,void* ptr)
#else
bool FileMonitor::onModify(struct pollfd eventData,void* ptr)
#endif
{
    struct inotify_event* event;
    //获取到实例
    char buf[BUFSIZ];
    int i = 0;
    struct stat file_buffer;
    bzero(buf,BUFSIZ);
    ssize_t read_size;
    int pipe_number;
    int res;
    int wd;
    int fd;
    int pipeFd;
    auto monitor = (FileMonitor*)ptr;

#ifdef _SYS_EPOLL_H
    fd = eventData.data.fd;
#else
    fd = eventData.fd;
#endif

    if(!file_node.pipe_collect)
    {
        LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","unixPipe is null");
        return  false;
    }


    read_size = read(fd,buf,BUFSIZ);
    if(read_size>0)
    {
        while(i<read_size)
        {
            event = (struct inotify_event*)&buf[i];


            bzero(&file_buffer, sizeof(file_buffer));
//            LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","mask:"<<event->mask);
            //如果说文件发生了修改事件
            if(event->mask & IN_MODIFY) {
                //选中一个管道的序号
                pipe_number = file_node.send_number%file_node.workerNumberCount;

                pipeFd = *(file_node.pipe_collect[pipe_number]+1);
                monitor->eventInstance->eventUpdate(pipeFd,EPOLLOUT|EPOLLET);

            }else if(event->mask & IN_ATTRIB)
            {
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

#ifdef _SYS_EPOLL_H
bool FileMonitor::onPipeWrite(struct epoll_event eventData,void* ptr)
#else
bool FileMonitor::onPipeWrite(struct pollfd eventData,void* ptr)
#endif
{
    int fd;
    ssize_t write_size;
    file_read file_data;
    ssize_t readLen;
    int res;
    struct stat file_buffer;
    auto monitor = (FileMonitor*)ptr;
    //读取变化之后的文件大小
    fd = eventData.data.fd;
    res = fstat(file_node.file_fd, &file_buffer);
    if (res == -1) {
        LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","fstat fd error");
        return false;
    }
    if(file_buffer.st_size>file_node.begin_length)
    {
        readLen = file_buffer.st_size - file_node.begin_length;

        bzero(&file_data, sizeof(file_data));

        file_data.begin = (size_t)(file_buffer.st_size);
        file_data.offset = readLen;


        if(file_node.pipe_collect)
        {
            write_size = write(fd,&file_data,sizeof(file_data));

            if(write_size<=0)
            {

                LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","Write Pipe Fd Error");
            }else{

                file_node.send_number++;
            }
        }
    }

    file_node.begin_length = file_buffer.st_size;
    monitor->eventInstance->eventUpdate(fd,EPOLLIN|EPOLLET);
}

