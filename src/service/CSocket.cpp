//
// Created by zhanglei on 19-8-13.
//

#include "include/MainService.h"

using service::CSocket;

CSocket::CSocket()
{
    socket_fd = socket(AF_INET,SOCK_STREAM,0);
}

int CSocket::setConfig(const char* ip,const char* port)
{
    if(ip)
    {
        strcpy(socketIp,ip);
    }

    if(port)
    {
        socketPort = (uint16_t)atoi(port);
    }
}

bool CSocket::connect() {

    socklen_t len;
    int res;
    //检查端口
    if(!socketPort)
    {
        LOG_TRACE(LOG_ERROR,false,"CSocket::connect","socket of port can not be empty");
        return  false;
    }

    //暂时只支持ipv4
    struct sockaddr_in client_address;

    client_address.sin_family = AF_INET;
    client_address.sin_port = htons(socketPort);

    len = sizeof(client_address);

    res = ::connect(socket_fd,(struct sockaddr*)&client_address,len);

    if(res == -1)
    {
        return  false;
    }else{
        return  true;
    }
}

bool CSocket::send(int fd,void* vptr,size_t n)
{
    size_t nleft;
    ssize_t nwrite;
    char* ptr;
    ptr = (char*)vptr;
    nleft = n;

    while(nleft > 0)
    {
        if((nwrite = ::send(fd,ptr,nleft,0)) > 0)
        {
            nleft -= nwrite;
        }else{
            if(errno == EINTR)
            {
                continue;
            }else{
                return  false;
            }
        }
    }

    return true;
}


int CSocket::getSocket()
{
    return socket_fd;
}


CSocket::~CSocket() {
    //关闭掉套接字
    if(socket_fd > 0) {
        close(socket_fd);
    }
}