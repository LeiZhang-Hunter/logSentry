//
// Created by zhanglei on 19-8-16.
//

#include "Common.h"
FileNode monitorFileNode;


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
    int res = this->createProcess();
    if(res != 0)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::start","create process error");
        return;
    }
//    this->run();
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
    bool init_result;
    bool result;
    int thread_number;
    FileMonitorWorker* socket_worker;

#ifdef __linux__
    if(prctl(PR_SET_PDEATHSIG, SIGTERM) != 0)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","prctl set error");
    }
#endif

    eventInstance = CSingleInstance<CEvent>::getInstance();
    sig_handle->setSignalHandle(SIGTERM,onStop);

    //检查线程数目设置的是否合理
    if(workerNumber<1)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","workerNumber  error");
        return;
    }

    //初始化节点工具
    init_result = monitorFileNode.initNode(workerNumber,monitorPath.c_str());
    if(!init_result)
    {
        return;
    }

    result = eventInstance->createEvent(1+workerNumber);
    if(!result)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","CEvent::createEvent error");
        return;
    }
    //添加钩子
    eventInstance->hookAdd(CEVENT_READ,onModify);

    eventInstance->hookAdd(CEVENT_WRITE,onPipeWrite);

    eventInstance->eventAdd(monitorFileNode.monitor_node.inotify_fd,EPOLLIN|EPOLLET);

    map<string,map<string,string>>mContent = config_instance->getConfig();


    //开始创建socket线程用来做读取后的数据收发
    for(thread_number=0;thread_number<workerNumber;thread_number++)
    {
        socket_worker = new FileMonitorWorker(mContent["server"],monitorFileNode.monitor_node.pipe_collect[thread_number][0]);

        socket_worker->filePath = monitorPath;

        socket_worker->fileName = fileName;

        //设置线程为守护线程
//        socket_worker->SetDaemonize();
        //启动线程
        socket_worker->Start();
        //加入到线程池中
        temp_pool[thread_number] = socket_worker;
        eventInstance->eventAdd(monitorFileNode.monitor_node.pipe_collect[thread_number][1],EPOLLET|EPOLLIN);
    }

    eventInstance->eventLoop(this);

    //释放掉之前启动的线程
    for(thread_number=0;thread_number<workerNumber;thread_number++)
    {
        //关闭线程标志位
        temp_pool[thread_number]->stopWorker();
        //释放线程
        temp_pool[thread_number]->ReleaseThread(nullptr);
        //释放掉内存
        delete (temp_pool[thread_number]);
        temp_pool.erase(thread_number);
    }

    //释放事件
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
    int fd;
    int pipeFd;
    auto monitor = (FileMonitor*)ptr;

#ifdef _SYS_EPOLL_H
    fd = eventData.data.fd;
#else
    fd = eventData.fd;
#endif

    if(!monitorFileNode.monitor_node.pipe_collect)
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
            //如果说文件发生了修改事件
            if(event->mask & IN_MODIFY) {
                //选中一个管道的序号
                pipe_number = monitorFileNode.monitor_node.send_number%monitor->workerNumber;

                pipeFd = *(monitorFileNode.monitor_node.pipe_collect[pipe_number]+1);
                //将描述符加入可写事件
                monitor->eventInstance->eventUpdate(pipeFd,EPOLLOUT|EPOLLET);

            }else if(event->mask & IN_MOVE_SELF)
            {
                //vim 可能会用新的文件覆盖掉旧的文件这里我们需要从监视队列中去掉旧的fd，然后加入新的fd监控
                //当出现删除文件这种情况
                monitorFileNode.deleteMonitor();

            }else if((event->mask & IN_DELETE) || (event->mask & IN_DELETE_SELF))
            {
                //删除文件监视
                monitorFileNode.deleteMonitor();
            }else if(event->mask & IN_ATTRIB){
                //rm -rf 可能会改变掉文件的属性
                monitorFileNode.deleteMonitor();

            }else if(event->mask & IN_IGNORED)
            {
                //再次将文件加入监控
                while(!(monitorFileNode.addMonitor()))
                {
                    LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","monitorFileNode->addMonitor error,error path:"<<monitor->monitorPath<<";fd:"<<monitorFileNode.monitor_node.file_fd);
                    sleep(1);
                }

                //关闭掉旧的文件描述符，防止描述符泄露
                if(monitorFileNode.monitor_node.file_fd > 0) {
                    close(monitorFileNode.monitor_node.file_fd);
                    //删除掉文件描述符
                }
                //用新的文件句柄,建造一个新的文件句柄
                monitorFileNode.monitor_node.file_fd = open(monitorFileNode.monitor_node.path,O_RDWR|O_CREAT,S_IRWXU);
                if(monitorFileNode.monitor_node.file_fd == -1)
                {
                    LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","open fd error");
                    return false;
                }

                //更新文件状态
                res = fstat(monitorFileNode.monitor_node.file_fd, &file_buffer);
                if (res == -1) {
                    LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","fstat fd error");
                    return false;
                }

                //更新文件起始读的位置
                monitorFileNode.setBeginLen(file_buffer.st_size);

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
    res = fstat(monitorFileNode.monitor_node.file_fd, &file_buffer);
    if (res == -1) {
        LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","fstat fd error");
        return false;
    }
    if((file_buffer.st_size>monitorFileNode.monitor_node.begin_length))
    {
        readLen = file_buffer.st_size - monitorFileNode.monitor_node.begin_length;

        bzero(&file_data, sizeof(file_data));

        file_data.begin = (size_t)(file_buffer.st_size);
        file_data.offset = readLen;
        file_data.fild_fd = monitorFileNode.monitor_node.file_fd;

        if(monitorFileNode.monitor_node.pipe_collect)
        {
            write_size = write(fd,&file_data,sizeof(file_data));

            if(write_size<=0)
            {

                LOG_TRACE(LOG_ERROR, false, "FileMonitor::onPipeWrite","Write Pipe Fd Error");
            }else{
                monitorFileNode.monitor_node.send_number++;
            }
        }
    }

    monitorFileNode.monitor_node.begin_length = file_buffer.st_size;
    monitor->eventInstance->eventUpdate(fd,EPOLLIN|EPOLLET);
}

