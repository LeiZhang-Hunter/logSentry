//
// Created by zhanglei on 19-8-16.
//

#include "../../include/Common.h"
enum {
    TEST,
    NN
};
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
    int fileNode = inotify_init();

    if(fileNode < 0)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","create process error,errcode:"<<errno<<";errmsg:"<<strerror(errno)<<";line:"<<__LINE__<<"\n");
        return;
    }

    int wd = inotify_add_watch(fileNode,monitorPath.c_str(),IN_MODIFY);
    if(wd == -1)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","create inotify_add_watch error,errcode:"<<errno<<";errmsg:"<<strerror(errno)<<";line:"<<__LINE__<<"\n");
        return;
    }

    bool result;
    CEvent* eventInstance = CSingleInstance<CEvent>::getInstance();
    result = eventInstance->createEvent(512);
    if(!result)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","CEvent::createEvent error,errcode:"<<errno<<";errmsg:"<<strerror(errno)<<";line:"<<__LINE__<<"\n");
        return;
    }
    printf("fileNode:%d\n",fileNode);
    eventInstance->eventAdd(fileNode,CEVENT_READ,onMonitor);

    eventInstance->eventLoop();
}

bool FileMonitor::setNotifyPath(string path) {
    monitorPath = path;
}

//在这里编写逻辑
void FileMonitor::run() {

}

//文件发生变化的逻辑在这里写
bool FileMonitor::onMonitor(struct epoll_event eventData) {
    struct inotify_event* event;
    //获取到实例
    CEvent* eventInstance =  CSingleInstance<CEvent>::getInstance();
    char buf[BUFSIZ];
    int i = 0;
    bzero(buf,BUFSIZ);
    ssize_t res = read(eventData.data.fd,buf,BUFSIZ);
    if(res>0)
    {
        while(i<res)
        {
            event = (struct inotify_event*)&buf[i];

            if(event->len) {
                printf("name=%s\n", event->name);
            }

            i+=(sizeof(struct inotify_event)+event->len);

            LOG_TRACE(LOG_SUCESS,true,"FileMonitor::onMonitor","cookie:"<<event->cookie<<";wd:"<<event->wd);
        }

    }


//    size_t buf_len=sizeof(inotify_event);
//    ssize_t res;
//    char buf[BUFSIZ];
//    struct inotify_event *event;
//    printf("fd:%d\n",eventData.data.fd);
//
//    switch(eventData.events)
//    {
//        case EPOLLIN:
//            res = read(eventData.data.fd,buf,sizeof(buf)-1);
//            printf("%ld\n",res);
//            if(res>0)
//            {
//                (struct inotify_event *)&buf[0];
//            }else{
//                printf("%s\n",strerror(errno));
//            }
//            break;
//    }
}