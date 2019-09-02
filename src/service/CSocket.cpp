//
// Created by zhanglei on 19-8-13.
//

#include "include/MainService.h"

using service::CSocket;

CSocket::CSocket()
{
    socket_fd = socket(AF_INET,SOCK_STREAM,0);
}

int CSocket::setConfig(map<string,string>baseConfig)
{
    socketConfig = baseConfig;
}

bool CSocket::connect() {

    socklen_t len;
    int res;
    //检查ip地址
    if(socketConfig["ip"].empty())
    {
        LOG_TRACE(LOG_ERROR,false,"CSocket::connect","CSocket->connect;line:"<<__LINE__<<";errmsg:socket of ip can not be empty");
        return  false;
    }

    //检查端口
    if(socketConfig["port"].empty())
    {
        LOG_TRACE(LOG_ERROR,false,"CSocket::connect",";line:"<<__LINE__<<";errmsg:socket of port can not be empty");
        return  false;
    }

    //暂时只支持ipv4
    struct sockaddr_in client_address;

    client_address.sin_family = AF_INET;
    client_address.sin_port = htons((uint16_t)atoi(socketConfig["port"].c_str()));

    len = sizeof(client_address);

    res = ::connect(socket_fd,(struct sockaddr*)&client_address,len);

    if(res == -1)
    {
        LOG_TRACE(LOG_ERROR,false,"CSocket::connect","CSocket->connect failed;errno:"<<(errno)<<";errmsg:"<<strerror(errno)<<";line:"<<__LINE__);
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
    close(socket_fd);
}