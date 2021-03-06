//
// Created by zhanglei on 19-8-8.
//

#include <zconf.h>

#ifndef SOCKETSENTRY_CTHREAD_H
#define SOCKETSENTRY_CTHREAD_H

#endif //SOCKETSENTRY_CTHREAD_H

namespace service{
class CThread{

    public:
        CThread();

        virtual ~CThread();

        //启动线程
        bool Start();

        //停止线程
        void Stop();

        //恢复线程
        void Resume();

        //线程的运行状态
        bool Status();

        //设置守护线程
        bool SetDaemonize();

        //释放线程
        bool ReleaseThread(void* status);

    protected:
        virtual void Execute();

    private:
        pthread_mutex_t mMutex;

        pthread_cond_t mCondLock;

        pthread_t mThreadID;

        uint8_t daemonize;

        static void* ThreadProc(void* arg);

        bool mTerminated;

        bool isSuspend = false;

        //线程运行状态
        bool mRunStatus;

        pthread_attr_t attr;

        CSignal mSigHandler;
};
}