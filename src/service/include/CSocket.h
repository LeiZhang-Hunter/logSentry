//
// Created by zhanglei on 19-8-13.
//

#ifndef LOGSENTRY_CSOCKET_H
#define LOGSENTRY_CSOCKET_H

#endif //LOGSENTRY_CSOCKET_H

using namespace std;
namespace service{
    class CSocket{
    public:
        CSocket();
        ~CSocket();

        //链接
        bool connect();

        //获取socket描述符
        int getSocket();

        //设置配置文件
        int setConfig(map<string,string>config);

    private:
        int socket_fd;
        map<string,string> socketConfig;
    };
}