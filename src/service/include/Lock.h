//
// Created by zhanglei on 19-8-13.
//

#ifndef LOGSENTRY_CMUTEXLOCK_H
#define LOGSENTRY_CMUTEXLOCK_H

#endif //LOGSENTRY_CMUTEKLOCK_H

//这是一个互斥锁的类
namespace service {

    //基础接口类 用来做接口
    class Lock{
        virtual int lock(){};
        virtual int lock(uint8_t flag){};
        virtual int unLock(){};
    };

    //互斥锁
    class CMutexLock :public Lock{

    public:
        CMutexLock(uint8_t shared_flag = 0);
        ~CMutexLock();
        int lock();
        int unLock();


    public:
        uint8_t shared_flag;
        pthread_mutex_t c_mutex;
        pthread_mutexattr_t c_mutex_attr;
    };


    //读写锁
    class CRwLock :public Lock{
    public:
        CRwLock(uint8_t shared_flag=0);
        ~CRwLock();
        int lock(uint8_t rwlock_flag);
        int unLock();

    private:
        pthread_rwlock_t rw_lock;
        pthread_rwlockattr_t rw_lock_attr;
    };
}