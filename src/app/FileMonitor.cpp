//
// Created by zhanglei on 19-8-16.
//

#include "../../include/Common.h"
using namespace app;
int FileMonitor::fileFd = 0;
ssize_t FileMonitor::beginLength = 0;
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
    struct stat buf;
    int fileNode;
    int wd;
    bool result;
    int thread_number;


    fileNode = inotify_init();

    if(fileNode < 0)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","create process error,errcode:"<<errno<<";errmsg:"<<strerror(errno)<<";line:"<<__LINE__<<"\n");
        return;
    }
    printf("%d\n",getpid());
    wd = inotify_add_watch(fileNode,monitorPath.c_str(),IN_MODIFY);
    if(wd == -1)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","create inotify_add_watch error,errcode:"<<errno<<";errmsg:"<<strerror(errno)<<";line:"<<__LINE__<<"\n");
        return;
    }

    CEvent* eventInstance = CSingleInstance<CEvent>::getInstance();
    result = eventInstance->createEvent(512);
    if(!result)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","CEvent::createEvent error,errcode:"<<errno<<";errmsg:"<<strerror(errno)<<";line:"<<__LINE__<<"\n");
        return;
    }
    printf("fileNode:%d\n",fileNode);
    eventInstance->eventAdd(fileNode,CEVENT_READ,onModify);

    //打开文件
    fileFd = open(monitorPath.c_str(),O_RDONLY);

    if(fileFd == -1)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","open fd error,errcode:"<<errno<<";errmsg:"<<strerror(errno)<<";line:"<<__LINE__<<"\n");
        return;
    }

    //加载文件的初始大小
    bzero(&buf, sizeof(buf));
    int res = fstat(fileFd,&buf);
    if(res == -1)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","fstat fd error,errcode:"<<errno<<";errmsg:"<<strerror(errno)<<";line:"<<__LINE__<<"\n");
        return;
    }

    //开始创建socket线程用来做读取后的数据收发
    for(thread_number=0;thread_number<workerNumber;thread_number++)
    {
        CThreadSocket* socket_worker = new CThreadSocket();
        //启动线程
        socket_worker->Start();
    }

    beginLength = buf.st_size;
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
    ssize_t readLen;
    bzero(buf,BUFSIZ);
    ssize_t res;
    ssize_t n;

    res = read(eventData.data.fd,buf,BUFSIZ);
    if(res>0)
    {
        while(i<res)
        {
            event = (struct inotify_event*)&buf[i];

            if(event->len) {
                printf("name=%s\n", event->name);
            }

            LOG_TRACE(LOG_SUCESS,true,"FileMonitor::onModify","cookie:"<<event->cookie<<";wd:"<<event->wd<<";mask:"<<event->mask);

            //如果说文件发生了修改事件
            if(event->mask & IN_MODIFY) {
                //读取变化之后的文件大小
                bzero(&file_buffer, sizeof(file_buffer));
                int res = fstat(fileFd, &file_buffer);
                if (res == -1) {
                    LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","fstat fd error,errcode:" << errno << ";errmsg:" << strerror(errno) << ";line:"<< __LINE__ << "\n");
                    return false;
                }

                if(file_buffer.st_size>beginLength)
                {
                    readLen = file_buffer.st_size - beginLength;

                    printf("readLen:%ld\n",readLen);
                    n = pread(fileFd, buf, (size_t)readLen,file_buffer.st_size-readLen);
                    buf[n] = '\0';
                    if(n>0)
                    {
                        printf("read:%s\n",buf);
                    }else if(n<0)
                    {
                        LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","fstat fd error,errcode:" << errno << ";errmsg:" << strerror(errno) << ";line:"<< __LINE__ << "\n");
                    }
                }

                beginLength = file_buffer.st_size;
            }

            i+=(sizeof(struct inotify_event)+event->len);
        }

    }
}