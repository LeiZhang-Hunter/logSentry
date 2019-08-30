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
        void Execute();
        virtual bool onConnect();
        virtual bool onClose();
        virtual bool onReceive();

    private:
        string ip;
        string port;
    };
}