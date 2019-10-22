//
// Created by zhanglei on 19-8-30.
//
#include "Common.h"
extern FileNode monitorFileNode;
//构造函数
FileMonitorWorker::FileMonitorWorker(map<string,string> socketConfig,int pipe_fd)
{
    netConfig=socketConfig;
    pipe = pipe_fd;
    int flags=fcntl(pipe,F_GETFL,0);
    fcntl(pipe,F_SETFL,flags|O_NONBLOCK);
}

bool FileMonitorWorker::onCreate() {
    mallopt(M_ARENA_MAX, 1);
    CSocket* client_handle = this->getSocketHandle();

    if(!client_handle)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitorWorker::onCreate","client_handle must not be null;");
        return  false;
    }

    client_fd = client_handle->getSocket();
    if(client_fd <= 0)
    {
        LOG_TRACE(LOG_ERROR,false,"FileMonitorWorker::onCreate","client_handle->getSocket failed;");
        return  false;
    }

    int flag=1;
    setsockopt(client_fd,SOL_SOCKET,SO_REUSEADDR,&flag, sizeof(flag));

    client_handle->setConfig(netConfig["ip"].c_str(),netConfig["port"].c_str());

    threadSocketEvent->hookAdd(CEVENT_READ,onReceive);
    threadSocketEvent->hookAdd(CEVENT_WRITE,onSend);
}

bool FileMonitorWorker::onConnect() {
    //加套接字加入事件循环
    threadSocketEvent->eventAdd(pipe,EPOLLIN|EPOLLET);
    threadSocketEvent->eventAdd(getSocketHandle()->getSocket(),EPOLLET|EPOLLIN);
}

bool FileMonitorWorker::onClientRead(int fd,char* buf)
{

}
#ifdef _SYS_EPOLL_H
bool FileMonitorWorker::onReceive(struct epoll_event event,void* ptr)
#else
    bool FileMonitorWorker::onReceive(struct pollfd event,void* ptr)
#endif
{
    int fd;
    ssize_t size;
    auto monitor = (FileMonitorWorker *) ptr;

#ifdef _SYS_EPOLL_H
    fd = event.data.fd;
#else
    fd = event.fd;
#endif
    char buf[BUFSIZ];
    if (fd != monitor->pipe) {

        client_read:

        size = read(fd, buf, sizeof(buf));

        if (size == 0) {
            monitor->reconnect();
        } else if (size < 0) {
            if (errno == EINTR) {//被信号中断
                goto client_read;
            } else {
                LOG_TRACE(LOG_ERROR,false,"FileMonitorWorker::onReceive","recv failed");
                return  false;
            }

        } else {
            monitor->onClientRead(fd,buf);
        }
    } else {
        while((size = read(fd, buf, sizeof(buf))))
        {
            if (size > 0) {
                monitor->onPipe(fd, buf, size);
            } else {

                if(errno == EINTR)
                {
                    continue;
                }

                if(errno == EAGAIN)
                {
                    break;
                }

                if(errno != EAGAIN)
                {
                    LOG_TRACE(LOG_ERROR,false,"[FileMonitorWorker::onReceive]","read pipe error");
                }
                break;
            }
        }

    }
}

//这个是pipe的处理逻辑
void FileMonitorWorker::onPipe(int fd, char *buf,ssize_t len) {
    file_read* data;
    ssize_t n;
    char read_buf[BUFSIZ];
    ssize_t result;
    bzero(&read_buf, sizeof(read_buf));
    data = (file_read*)buf;//数据
    size_t  buf_len;//buf的长度
    ssize_t offset;//偏移量
    int res;//结果集
    string json_string;//存储json的缓冲
    size_t* protoBuf;//协议buffer存储地址
    const char* json_buffer;

    //检查是否发过来的描述符有效如果无效就干脆不要进行读取了
    res = fcntl(data->fild_fd,F_GETFL);
    if(res == -1)
    {
        //这个全局的值不会有写入，所以可以直接读取
        LOG_TRACE(LOG_ERROR,false,"FileMonitor::onPipe","fcntl fd error;path:"<<monitorFileNode.monitor_node.path);
        return;
    }

    do{
        if(data->offset > BUFSIZ)
        {
            offset = BUFSIZ;
        }else{
            offset = data->offset;
        }
        //多线程竞争读，防止出现内存错误复制描述符下发，防止出现资源的争取
        n = pread(data->fild_fd, read_buf,  (size_t)offset, data->begin-offset);
        read_buf[n] = '\0';

        if(n>0)
        {
            //进行协议封装
            Json::Value proto_builder;
            proto_builder["type"] = "sentry-log";
            proto_builder["file_name"] = fileName.c_str();
            proto_builder["buf_body"] = read_buf;
            proto_builder["monitor_type"] = "file";
            proto_builder["time"] = os->getUnixTime();

            //进行json压缩,这个是消息体
            json_string = jsonTool.jsonEncode(proto_builder);
            json_buffer = json_string.c_str();
            protoBuf = protoTool.encodeProtoStruct(json_buffer);
            buf_len = protoTool.getProtoLen();

            //封装协议,拼装包头和包体
            result = sendData(client_fd,protoBuf,buf_len);
            free((void*)protoBuf);
            if(result < 0 )
            {
                LOG_TRACE(LOG_ERROR,false,"FileMonitor::onPipe","send msg failed");
            }
        }else if(n<0)
        {
            LOG_TRACE(LOG_ERROR, false, "FileMonitor::onPipe","pread fd error");
        }
        data->offset -= n;
    }while(data->offset > 0);
}

bool FileMonitorWorker::onSend(struct epoll_event event, void *ptr) {
//    printf("send\n");
//    int fd;
//    ssize_t result;
//    fd = event.data.fd;
//    auto monitor = (FileMonitorWorker *) ptr;
//    char sendBuff[BUFSIZ];
//    strcpy(sendBuff,monitor->sendBuffer.c_str());
//    result = monitor->sendData(fd,sendBuff,strlen(sendBuff));
//    monitor->sendBuffer.clear();
//    if(result < 0 )
//    {
//        LOG_TRACE(LOG_ERROR,false,"FileMonitor::onModify","send msg failed");
//    }
//    monitor->threadSocketEvent->eventUpdate(fd,EPOLLIN|EPOLLET);
}

bool FileMonitorWorker::onClose() {
    threadSocketEvent->eventDelete(client_fd);
    threadSocketEvent->eventDelete(pipe);
}

FileMonitorWorker::~FileMonitorWorker(){
}