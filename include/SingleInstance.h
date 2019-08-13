//
// Created by zhanglei on 19-8-13.
//

#include "Lock.h"

#ifndef LOGSENTRY_SINGLEINSTANCE_H
#define LOGSENTRY_SINGLEINSTANCE_H

#endif //LOGSENTRY_SINGLEINSTANCE_H


namespace service {

    template<class T>
    class SingleInstance {

    public:
        //获取实例
        static T *getInstance(void) {
            if (instance == nullptr) {
                //加锁
                CMutexLock guard(0);
                guard.lock();
                if(instance == nullptr) {
                    instance = new T();
                }
                guard.unLock();
            }
            return instance;
        }

    private:
        static T *instance;
    };

    template <class T>
    T* SingleInstance<T>::instance = 0;
}
