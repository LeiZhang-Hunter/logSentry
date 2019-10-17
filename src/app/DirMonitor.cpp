//
// Created by zhanglei on 19-9-19.
//

#include "Common.h"

DirMonitor::DirMonitor()
{

}


void DirMonitor::start() {
    //创建worker
//    int res = this->createProcess();
//
//    if(res != 0)
//    {
//        LOG_TRACE(LOG_ERROR,false,"FileMonitor::start","create process error");
//        return;
//    }
    run();
}

bool DirMonitor::setFileName(const char* file_name)
{
    fileName = (file_name);
}

string DirMonitor::getFileName()
{
    return fileName;
}


bool DirMonitor::setWorkerNumber(int number)
{
    workerNumber = number;
}

bool DirMonitor::setNotifyPath(const char* path)
{
    monitorPath = path;
}

string DirMonitor::getNotifyPath()
{
    return monitorPath;
}

int DirMonitor::getWorkerNumber()
{
    return workerNumber;
}

//执行程序的入口
void DirMonitor::run()
{

    //创建一个wd
    inotify_fd = inotify_init();
    if(inotify_fd<= 0)
    {
        LOG_TRACE(LOG_ERROR,false,"DirMonitor::run","inotify init failed");
        return;
    }
    inotify_wd = inotify_add_watch(inotify_fd,monitorPath.c_str(),IN_CREATE|IN_DELETE_SELF|IN_DELETE|IN_MODIFY|IN_ATTRIB);
    if(inotify_wd <= 0)
    {
        LOG_TRACE(LOG_ERROR,false,"DirMonitor::run","inotify add watch error");
        return;
    }

    //初始化目录下的所有文件，并且加入到描述符池中，进行记录
    dirHandle = opendir(monitorPath.c_str());
    if(!dirHandle)
    {
        LOG_TRACE(LOG_ERROR,false,"DirMonitor::run","get opendir failed");
        return;
    }

    eventInstance = new CEvent();

    eventInstance->createEvent(10);

    eventInstance->hookAdd(CEVENT_READ,onChange);

    eventInstance->hookAdd(CEVENT_WRITE,onSend);


    eventInstance->eventAdd(inotify_fd,EPOLLET|EPOLLIN);

    string buffer;
    char file[PATH_MAX];
    int monitorFileFd;
    int pipe[2];
    int res;
    int workerNum;
    DirMonitorWorker* worker_object;

    //在站上申请一个内存池
    pipe_collect=(int(*)[2])calloc((size_t)workerNumber,sizeof(pipe));

    map<string,map<string,string>>mContent = config_instance->getConfig();

    //创建指定数目的管道
    for(workerNum = 0;workerNum<workerNumber;workerNum++)
    {
        res = socketpair(AF_UNIX,SOCK_DGRAM,0,pipe_collect[workerNum]);
        if(res == -1)
        {
            LOG_TRACE(LOG_ERROR,false,"DirMonitor::run","socketpair  failed");
            continue;
        }
        //加入可写事件的监控
        eventInstance->eventAdd(pipe_collect[workerNum][1],EPOLLET|EPOLLIN);

        //创建工作线程用来处理变化
        worker_object = new DirMonitorWorker(mContent["server"],pipe_collect[workerNum][0]);
        worker_object->SetDaemonize();
        worker_object->Start();
    }


    file_dir_data dataUnit;

    struct stat file_state;

    //遍历加入文件池中
    while((dirEntry = readdir(dirHandle)))
    {
        if(dirEntry->d_type == DT_REG)
        {
            bzero(&file,sizeof(file));
            snprintf(file,sizeof(file),"%s/%s",monitorPath.c_str(),dirEntry->d_name);
            buffer = file;

            monitorFileFd = open(file,O_CREAT|O_RDWR,S_IRWXU);
            if(monitorFileFd <= 0)
            {
                LOG_TRACE(LOG_ERROR,false,"DirMonitor::run","open file error");
                continue;
            }

            res = fstat(monitorFileFd,&file_state);
            if(res == -1)
            {
                LOG_TRACE(LOG_ERROR,false,"DirMonitor::run","fstat file error");
                continue;
            }

            fileDirPool[dirEntry->d_name] = monitorFileFd;

            bzero(&dataUnit, sizeof(dataUnit));

            dataUnit.begin = file_state.st_size;

            dataUnit.file_fd = monitorFileFd;

            //查看文件这个时候的大小来记录begin值，当发生变化的时候可以进行变动更新
            strcpy(dataUnit.name,dirEntry->d_name);

            //加入到我的池子中
            fileDataPool[monitorFileFd] = dataUnit;
            buffer.clear();
        }
    }



    eventInstance->eventLoop(this);
}

bool DirMonitor::onChange(struct epoll_event eventData,void* ptr)
{
    char buf[BUFSIZ];
    ssize_t read_size;
    int fd;
    int i = 0;
    struct inotify_event* event;
    auto dir_monitor = (DirMonitor*)ptr;
    int change_fd;
    int pipe_number;
    int pipeFd;

#ifdef _SYS_EPOLL_H
    fd = eventData.data.fd;
#else
    fd = eventData.fd;
#endif

    read_size = read(fd,buf,BUFSIZ);

    if(read_size > 0)
    {
        while(i<read_size){
            event = (struct inotify_event*)&buf[i];
            //文件发生变动
            if(event->mask & IN_MODIFY)
            {
                change_fd = dir_monitor->fileDirPool[event->name];
                //将这个变化事件加入队列池
                dir_monitor->eventPool.push_front(change_fd);

                //随机获取一个管道发送
                pipe_number = dir_monitor->send_number%dir_monitor->getWorkerNumber();

                pipeFd = *(dir_monitor->pipe_collect[pipe_number]+1);

                //把事件设置为可写事件
                dir_monitor->eventInstance->eventUpdate(pipeFd,EPOLLOUT|EPOLLET);

                dir_monitor->send_number++;

                printf("pipeFd:%d;pipe_number:%d;number:%d\n",pipeFd,pipe_number,dir_monitor->send_number);

            }else if(event->mask & IN_ATTRIB)
            {//文件属性发生变动

            }else if(event->mask & IN_DELETE || event->mask & IN_DELETE_SELF)
            {//文件发生删除操作

                dir_monitor->deleteMonitorByName(event->name);

            }else if(event->mask & IN_CREATE)
            {//文件发生创建操作
                dir_monitor->createMonitorByName(event->name);
            }
            i+=(sizeof(struct inotify_event)+event->len);
        }
    }
}

//添加文件节点到哨兵
bool DirMonitor::createMonitorByName(const char* name)
{

}

//从哨兵处删除节点
bool DirMonitor::deleteMonitorByName(const char* name)
{
    if(!name)
    {
        return false;
    }

    //从池子里删除掉这个文件
    int delete_fd = fileDirPool[name];
    //清除掉
    fileDataPool.erase(delete_fd);
    //清除掉内容
    fileDirPool.erase(name);

    return true;
}

//数据应该发送的时候
bool DirMonitor::onSend(struct epoll_event eventData, void *ptr)
{
    int change_fd;
    int event_fd;
    int res;
    ssize_t  write_size;
    auto dir_monitor = (DirMonitor*)ptr;
    ssize_t readLen;
    while((change_fd = dir_monitor->eventPool.back()))
    {
        //观察文件的变化尺寸
        dir_monitor->eventPool.pop_back();
        struct stat file_buffer;
        event_fd = eventData.data.fd;
        res = fstat(change_fd, &file_buffer);
        if (res == -1) {
            LOG_TRACE(LOG_ERROR, false, "DirMonitor::onModify","fstat fd error");
            return false;
        }

        auto dir_file_node = dir_monitor->fileDataPool[change_fd];

        //申请一个新的结构体
        readLen = file_buffer.st_size-dir_file_node.begin;

        dir_monitor->fileDataPool[change_fd].begin = file_buffer.st_size;

        //给要读的偏移量赋值
        dir_file_node.begin = file_buffer.st_size;

        dir_file_node.offset = readLen;

        write_size = write(event_fd,&dir_file_node,sizeof(file_dir_data));
        if(write_size<=0)
        {
            LOG_TRACE(LOG_ERROR, false, "DirMonitor::onSend","Write Pipe Fd Error");
        }
    }
}