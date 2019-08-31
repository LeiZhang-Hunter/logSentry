//
// Created by zhanglei on 19-8-16.
//

#include "../../include/Common.h"
using namespace app;
int FileMonitor::fileFd = 0;
ssize_t FileMonitor::beginLength = 0;
int workerNumberCount = 0;
int(*unixPipe)[2] = NULL;
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
    int pipe[2];


    fileNode = inotify_init();

    if(fileNode < 0)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","create process error,errcode:"<<errno<<";errmsg:"<<strerror(errno)<<";line:"<<__LINE__<<"\n");
        return;
    }
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

    if(workerNumber<1)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::run","workerNumber  error,errcode:"<<errno<<";errmsg:"<<strerror(errno)<<";line:"<<__LINE__<<"\n");
        return;
    }

    Config* instance = CSingleInstance<Config>::getInstance();
    map<string,map<string,string>>mContent = instance->getConfig();

    //开始创建socket线程用来做读取后的数据收发
    unixPipe = (int(*)[2])calloc((size_t)workerNumber,sizeof(pipe));
    workerNumberCount = workerNumber;
    for(thread_number=0;thread_number<workerNumber;thread_number++)
    {

        res = socketpair(AF_UNIX,SOCK_DGRAM,0,unixPipe[thread_number]);
        if(res == -1)
        {
            LOG_TRACE(LOG_ERROR,false,"FileMonitor::start","socketpair  failed,errcode:"<<errno<<";errmsg:"<<strerror(errno)<<";line:"<<__LINE__<<"\n");
            continue;
        }
        CThreadSocket* socket_worker = new FileMonitorWorker(mContent["server"],unixPipe[thread_number][0]);
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
    file_read file_data;
    ssize_t readLen;
    bzero(buf,BUFSIZ);
    ssize_t read_size;
    int pipe_number;
    int send_number = 0;
    int pipe;
    ssize_t write_size;
    int res;

    if(!unixPipe)
    {
        LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","unixPipe is null,errcode:" << errno << ";errmsg:" << strerror(errno) << ";line:"<< __LINE__ << "\n");
        return  false;
    }

//    for(pipe_number =0;pipe_number<workerNumberCount;pipe_number++)
//    {
//         cout<<"number:"<<pipe_number<<";"<<<<"\n";
//    }

    read_size = read(eventData.data.fd,buf,BUFSIZ);
    if(read_size>0)
    {
        while(i<read_size)
        {
            event = (struct inotify_event*)&buf[i];

            if(event->len) {
                printf("name=%s\n", event->name);
            }

//            LOG_TRACE(LOG_SUCESS,true,"FileMonitor::onModify","cookie:"<<event->cookie<<";wd:"<<event->wd<<";mask:"<<event->mask);

            //如果说文件发生了修改事件
            if(event->mask & IN_MODIFY) {
                //读取变化之后的文件大小
                bzero(&file_buffer, sizeof(file_buffer));
                res = fstat(fileFd, &file_buffer);
                if (res == -1) {
                    LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","fstat fd error,errcode:" << errno << ";errmsg:" << strerror(errno) << ";line:"<< __LINE__ << "\n");
                    return false;
                }

                if(file_buffer.st_size>beginLength)
                {
                    readLen = file_buffer.st_size - beginLength;

                    bzero(&file_data, sizeof(file_data));

                    file_data.begin = (size_t)(file_buffer.st_size-readLen);
                    file_data.offset = readLen;

                    pipe_number = send_number%workerNumberCount;

                    if(unixPipe)
                    {
                        pipe = *(unixPipe[pipe_number]+1);
                        if(pipe > 0)
                        {
                            write_size = write(pipe,&file_data,sizeof(file_data));
                            if(write_size<=0)
                            {
                                LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","Write Pipe Fd Error");
                            }else{
                                send_number++;
                            }
                        }else{
                            LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","Get Pipe Fd Error");
                            return false;
                        }
                    }


                }

                beginLength = file_buffer.st_size;
            }

            i+=(sizeof(struct inotify_event)+event->len);
        }

    }
}