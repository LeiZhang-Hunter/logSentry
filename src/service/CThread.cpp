//
// Created by zhanglei on 19-8-8.
//
#include "../../include/MainService.h"
service::CThread::CThread() {
    //初始化互斥锁
    pthread_mutex_init(&mMutex, nullptr);
    pthread_cond_init(&mCondLock,nullptr);
}

//销毁条件变量和锁
service::CThread::~CThread() {
    pthread_mutex_destroy(&mMutex);
    pthread_cond_destroy(&mCondLock);
}

//启动线程
bool service::CThread::Start() {
    /*
     * 初始化一个锁属性
     */
    pthread_attr_t attr;
    pthread_attr_init(&attr);

    bool mRunStatus = false;
    /*
     *启动线程
     */
    if(pthread_create(&mThreadID,&attr,ThreadProc,this) == 0)
    {
        mRunStatus=true;
    }
    /*
     *释放属性
     */
    pthread_attr_destroy(&attr);
    return mRunStatus;
}

//线程的运行状态
bool service::CThread::Status() {
    return mRunStatus;
}

void service::CThread::Stop() {
    mTerminated = true;
}

void service::CThread::Resume() {
    isSuspend = true;
}

void  service::CThread::Execute() {

}


//线程运行的主体程序
void* service::CThread::ThreadProc(void* arg)
{
    CThread* selfThread = (CThread*)arg;

    selfThread->mTerminated = false;

    //如果说没有被停止
    while(!selfThread->mTerminated)
    {
        if(selfThread->isSuspend)
        {
            pthread_mutex_lock(&selfThread->mMutex);
            pthread_cond_wait(&selfThread->mCondLock,&selfThread->mMutex);
            pthread_mutex_unlock(&selfThread->mMutex);
        }
        //运行主题程序
        selfThread->Execute();
    }

    return (void*)0;
}

