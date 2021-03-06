//
// Created by zhanglei on 19-8-13.
//

#include "MainService.h"

using service::CMutexLock;
using service::CRwLock;
using service::CLock;

/**
 *
 * @param shared_flag
 */
//初始化互斥索
CMutexLock::CMutexLock(uint8_t shared_flag)
{
    pthread_mutexattr_init(&c_mutex_attr);

    if(shared_flag == LOCK_PROCESS_IS_SHARED) {
        pthread_mutexattr_setpshared(&c_mutex_attr,PTHREAD_PROCESS_SHARED);
    }

    pthread_mutex_init(&c_mutex,&c_mutex_attr);
}

//加锁
int CMutexLock::lock() {
    while (1) {
        //防止死锁
        int res = pthread_mutex_lock(&c_mutex);
        if(res == 0)
        {
            break;
        }
        sleep(1);
        LOG_TRACE(LOG_WARING,FALSE,"CMutexLock::lock","lock failed");
    }
    return 0;
}

//解锁
int CMutexLock::unLock() {
    int res;
    while(1) {
        //防止死锁
        res = pthread_mutex_unlock(&c_mutex);
        if(res == 0)
        {
            break;
        }
        LOG_TRACE(LOG_WARING,FALSE,"CMutexLock::unLock","unlock failed");
        sleep(1);
    }
    return res;
}

//析构函数
CMutexLock::~CMutexLock()
{
    //释放属性锁
    pthread_mutexattr_destroy(&c_mutex_attr);
    pthread_mutex_destroy(&c_mutex);
}

using service::CRwLock;

CRwLock::CRwLock(uint8_t shared_flag) {
    pthread_rwlockattr_init(&rw_lock_attr);
    //在进程之间共享
    if(shared_flag == LOCK_PROCESS_IS_SHARED) {
        pthread_rwlockattr_setpshared(&rw_lock_attr, PTHREAD_PROCESS_SHARED);
    }
    pthread_rwlock_init(&rw_lock,&rw_lock_attr);
}

CRwLock::~CRwLock() {
    pthread_rwlockattr_destroy(&rw_lock_attr);
    pthread_rwlock_destroy(&rw_lock);
}

int CRwLock::lock(uint8_t rwlock_flag) {
    if(rwlock_flag == READ_LOCK)
    {
        pthread_rwlock_rdlock(&rw_lock);
    }else{
        pthread_rwlock_wrlock(&rw_lock);
    }
}

int CRwLock::unLock() {
    pthread_rwlock_unlock(&rw_lock);
}

