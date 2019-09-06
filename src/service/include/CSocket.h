//
// Created by zhanglei on 19-8-13.
//

#ifndef LOGSENTRY_CSOCKET_H
#define LOGSENTRY_CSOCKET_H

#endif //LOGSENTRY_CSOCKET_H

#include <arpa/inet.h>

using namespace std;
namespace service{
    class CSocket{
    public:
        CSocket();
        ~CSocket();

        //链接
        bool connect(int nsec);

        //获取socket描述符
        int getSocket();

        //设置配置文件
        int setConfig(const char* ip,const char* port);

        bool send(int fd,void* vptr,size_t n);



    private:
        int socket_fd;
        char socketIp[INET_ADDRSTRLEN];
        uint16_t socketPort;
    };
}