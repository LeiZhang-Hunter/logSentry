//
// Created by root on 19-10-2.
//

#include "Common.h"

DirMonitorWorker::DirMonitorWorker(map<string,string> socketConfig,int pipe_fd) {
    netConfig = socketConfig;
    pipe = pipe_fd;
    int flags=fcntl(pipe,F_GETFL,0);
    fcntl(pipe,F_SETFL,flags|O_NONBLOCK);
}

bool DirMonitorWorker::onCreate()
{
    mallopt(M_ARENA_MAX, 1);
    CSocket* client_handle = this->getSocketHandle();
    if(!client_handle)
    {
        LOG_TRACE(LOG_ERROR,false,"DirMonitorWorker::onCreate","client_handle must not be null;");
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
}

bool DirMonitorWorker::onConnect()
{
    //加套接字加入事件循环
    threadSocketEvent->eventAdd(pipe,EPOLLIN|EPOLLET);
    threadSocketEvent->eventAdd(getSocketHandle()->getSocket(),EPOLLET|EPOLLIN);
}

bool DirMonitorWorker::onClose()
{
    threadSocketEvent->eventDelete(client_fd);
    threadSocketEvent->eventDelete(pipe);
}

bool DirMonitorWorker::onReceive(struct epoll_event event,void* ptr)
{
    int fd = event.data.fd;
    auto dir_monitor = (DirMonitorWorker*)ptr;
    int pipe = dir_monitor->pipe;
    ssize_t read_size;

    if(fd == pipe)
    {
        file_dir_data data_node;
        while((read_size = read(fd,&data_node,sizeof(data_node))))
        {
            if (read_size > 0) {

                //收到管道的数据
                dir_monitor->onPipe(fd, &data_node, sizeof(data_node));
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
        if(read_size>0)
        {

        }else{
            if(errno != EAGAIN) {
                LOG_TRACE(LOG_ERROR, false, "DirMonitorWorker::onReceive", "read size failed;read size:" << read_size);
            }
        }
    }else{
        char buf[BUFSIZ];
        //返回的是客户端套接字
        client_read:
        read_size = read(fd, buf, sizeof(buf));
        if(read_size == 0)
        {
            dir_monitor->reconnect();
        }else if(read_size < 0)
        {
            if (errno == EINTR) {//被信号中断
                goto client_read;
            } else {
                LOG_TRACE(LOG_ERROR,false,"FileMonitorWorker::onReceive","recv failed");
            }
        }

    }
}

bool DirMonitorWorker::stopWorker()
{
    //关闭重连
    getSocketHandle()->setConnectFlag(0);
    //停止事件循环
    threadSocketEvent->stopLoop();
    //关闭执行线程
    Stop();
}

void DirMonitorWorker::onPipe(int fd, file_dir_data *node,size_t len) {
    ssize_t n;
    char read_buf[BUFSIZ];
    ssize_t result;
    bzero(&read_buf, sizeof(read_buf));
    ssize_t offset;
    string json_string;//存储json的string
    size_t buf_len;
    do{
        if(node->offset > BUFSIZ)
        {
            offset = BUFSIZ;
        }else{
            offset = node->offset;
        }
        n = pread(node->file_fd, read_buf,  (size_t)offset, node->begin-offset);

        if(n>0)
        {
            //进行协议封装
            Json::Value proto_builder;
            proto_builder["type"] = "sentry-log";
            proto_builder["monitor_type"] = "dir";
            proto_builder["dir_name"] = dirMonitorName;
            proto_builder["file_name"] = node->name;
            proto_builder["file_dir"] = monitorDir.c_str();
            proto_builder["buf_body"] = read_buf;
            proto_builder["time"] = os->getUnixTime();
            json_string = jsonTool.jsonEncode(proto_builder);
            auto protoBuf = protoTool.encodeProtoStruct(json_string.c_str());
            buf_len = protoTool.getProtoLen();
            result = sendData(client_fd,protoBuf,buf_len);
            free(protoBuf);

            if(result < 0 )
            {
                LOG_TRACE(LOG_ERROR,false,"FileMonitor::onModify","send msg failed");
                break;
            }
        }else if(n<0)
        {

            LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","pread fd error");
            break;
        }
        node->offset -= n;
    }while(node->offset > 0);
}

DirMonitorWorker::~DirMonitorWorker()
{

}