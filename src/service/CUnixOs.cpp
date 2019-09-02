//
// Created by zhanglei on 19-8-23.
//
#include "include/MainService.h"

using namespace service;

bool CUnixOs::getRlimit(int resource,struct rlimit *rlim) {
    if(!rlim)
    {
        LOG_TRACE(LOG_ERROR,false,"CUnixOs::getRlimit","rlimit is not NULL;in line:"<<__LINE__);
        return  false;
    }

    int res;

    if((getrlimit(resource,rlim)) == 0)
    {
        return  true;
    }else{
        LOG_TRACE(LOG_ERROR,false,"CUnixOs::getRlimit","getRlimit failed");
        return  false;
    }
}


