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
//    threadSocketEvent->hookAdd(CEVENT_WRITE,onSend);
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
            LOG_TRACE(LOG_ERROR,false,"DirMonitorWorker::onReceive","read size failed;read size:"<<read_size);
        }
    }else{

    }
}

void DirMonitorWorker::onPipe(int fd, file_dir_data *node,size_t len) {
    ssize_t n;
    char read_buf[BUFSIZ];
    ssize_t result;
    bzero(&read_buf, sizeof(read_buf));
    ssize_t offset;
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

            result = sendData(client_fd,read_buf,sizeof(read_buf));

            if(result < 0 )
            {
                LOG_TRACE(LOG_ERROR,false,"FileMonitor::onModify","send msg failed");
            }
        }else if(n<0)
        {
            LOG_TRACE(LOG_ERROR, false, "FileMonitor::onModify","pread fd error");
        }
        node->offset -= n;
    }while(node->offset > 0);
}

DirMonitorWorker::~DirMonitorWorker()
{

}