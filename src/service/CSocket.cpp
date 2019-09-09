//
// Created by zhanglei on 19-8-13.
//

#include "include/MainService.h"

using service::CSocket;

CSocket::CSocket()
{
    socket_fd = socket(AF_INET,SOCK_STREAM,0);
    connectFlag = 1;
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

bool CSocket::connect(int nsec) {

    socklen_t len;
    int res;
    int flags,n,error;
    fd_set rset,wset;
    //暂时只支持ipv4
    struct sockaddr_in client_address;
    struct timeval tval;
    //检查端口
    if(!socketPort)
    {
        LOG_TRACE(LOG_ERROR,false,"CSocket::connect","socket of port can not be empty");
        return  false;
    }

    flags = fcntl(socket_fd,F_GETFL,0);

    fcntl(socket_fd,F_SETFL,flags|O_NONBLOCK);

    error = 0;

    client_address.sin_family = AF_INET;
    client_address.sin_port = htons(socketPort);
    client_address.sin_addr.s_addr = inet_addr(socketIp);
    len = sizeof(client_address);

    res = ::connect(socket_fd,(struct sockaddr*)&client_address,len);

    if(res<0)
    {
        if(errno != EINPROGRESS)
        {
            return  false;
        }
    }

    if(res == 0)
    {
        return  true;
    }

    FD_ZERO(&rset);

    FD_SET(socket_fd,&rset);

    wset = rset;

    tval.tv_sec = nsec;
    tval.tv_usec = 0;

    if((n=select(socket_fd+1,&rset,&wset, nullptr,nsec ? &tval : nullptr)) == 0)
    {
        errno = ETIMEDOUT;
        close(socket_fd);
        return  false;
    }

    if(FD_ISSET(socket_fd,&rset) || FD_ISSET(socket_fd,&wset))
    {
        len = sizeof(error);

        if(getsockopt(socket_fd,SOL_SOCKET,SO_ERROR,&error,&len) < 0)
        {
            return  false;
        }
    }else{
        LOG_TRACE(LOG_ERROR,false,"CSocket::connect","socket fd not set");
        return  false;
    }

    fcntl(socket_fd,F_SETFL,flags);

    if(error)
    {
        close(socket_fd);
        errno = error;
        return  false;
    }

    return  true;
}

//重新连接
bool CSocket::reconnect()
{
    int res;
    //释放掉socketfd
    while(connectFlag) {
        //防止cpu刷的过高
        sleep(2);
        res = ::close(socket_fd);
        if (!res) {
            LOG_TRACE(LOG_ERROR, false, "CSocket::reconnect", "close socket failed");
            continue;
        }
        socket_fd = socket(AF_INET, SOCK_STREAM, 0);
        if (socket_fd < 0) {
            LOG_TRACE(LOG_ERROR, false, "CSocket::reconnect", "create socket failed");
            continue;
        }

        res = this->connect(2000);
        if(res == 0)
        {
            continue;
        }

        //重新连接成功
        break;
    }

    return  true;
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

bool CSocket::setConnectFlag(uint8_t flag)
{
    connectFlag = flag;
}


int CSocket::getSocket()
{
    return socket_fd;
}


CSocket::~CSocket() {
    connectFlag = 0;
    //关闭掉套接字
    if(socket_fd > 0) {
        close(socket_fd);
    }
}