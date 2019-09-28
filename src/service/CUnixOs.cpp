//
// Created by zhanglei on 19-8-23.
//
#include "MainService.h"

using namespace service;

bool CUnixOs::getRlimit(int resource,struct rlimit *rlim) {
    if(!rlim)
    {
        LOG_TRACE(LOG_ERROR,false,"CUnixOs::getRlimit","rlimit is not NULL;");
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


bool CUnixOs::is_file(const char *dir)
{
    struct stat inode_stat;
    int res = stat(dir,&inode_stat);
    if(res == 0)
    {
        if(S_ISREG(inode_stat.st_mode))
        {
            return true;
        }else{
            return  false;
        }

    }else{
        return  -1;
    }
}

bool CUnixOs::is_dir(const char *dir)
{
    struct stat inode_stat;
    int res = stat(dir,&inode_stat);
    if(res == 0)
    {
        if(S_ISDIR(inode_stat.st_mode))
        {
            return true;
        }else{
            return  false;
        }

    }else{
        return  -1;
    }
}