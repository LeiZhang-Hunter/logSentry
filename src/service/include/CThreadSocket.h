//
// Created by zhanglei on 19-8-30.
//

#ifndef LOGSENTRY_CTHREADSOCKET_H
#define LOGSENTRY_CTHREADSOCKET_H

#endif //LOGSENTRY_CTHREADSOCKET_H
using namespace std;
namespace service{
    class CThreadSocket : public CThread{
    public:
        CThreadSocket();
        ~CThreadSocket() override;
        void Execute() override;
        //创建套接字的时候
        virtual bool onCreate(){

        };
        virtual bool onConnect(){};
        virtual bool onClose(){};
        virtual bool onReceive(int fd,char* buf,size_t len){};
        CSocket* getSocketHandle();
        struct epoll_event*eventCollect;
        //发送数据
        ssize_t sendData(int fd,void* vptr,size_t n);
        //重连
#ifdef _SYS_EPOLL_H
        bool reconnect();
#else
        bool reconnect(int fd,short flags);
#endif
        CEvent* threadSocketEvent;

    private:
        CSocket* socketHandle;
        static CThreadSocket* instance;
        string ip;
        string port;
        int eventfd;
        int run=0;
    };
}