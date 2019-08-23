//
// Created by zhanglei on 19-8-13.
//

#include "CLock.h"

#ifndef LOGSENTRY_CSINGLEINSTANCE_H
#define LOGSENTRY_CSINGLEINSTANCE_H

#endif //LOGSENTRY_SINGLEINSTANCE_H


namespace service {

    template<class T>
    class CSingleInstance {

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
    T* CSingleInstance<T>::instance = 0;
}