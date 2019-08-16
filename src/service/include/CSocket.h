//
// Created by zhanglei on 19-8-13.
//

#ifndef LOGSENTRY_CSOCKET_H
#define LOGSENTRY_CSOCKET_H

#endif //LOGSENTRY_CSOCKET_H

#include <sys/socket.h>

namespace service{
    class CSocket{
    public:
        CSocket();
        ~CSocket();

        //链接
        int connect();

        //绑定
        int bind();

        //监听
        int listen(int backLog);

        //接受
        int accept();

        //添加配置选项
        int addOption();

        //获取socket描述符
        int getSocket();

    private:
        int socket_fd;
    };
}