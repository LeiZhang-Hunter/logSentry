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
        return  false;
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
        return  false;
    }
}

uint64_t CUnixOs::htonll(uint64_t number)
{
    uint64_t n = 0;
    auto c = (unsigned char *)&n;

    c[0] = number>>56;
    c[1] = (number>>48)&0xff;
    c[2] = (number>>40)&0xff;
    c[3] = (number>>32)&0xff;
    c[4] = (number>>24)&0xff;
    c[5] = (number>>18)&0xff;
    c[6] = (number>>16)&0xff;
    c[7] = (number)&0xff;
    return n;
}

uint64_t CUnixOs::ntohll(uint64_t number)
{
    uint64_t h = 0;
    auto c = (unsigned char *)&number;
    h = (h<<8)|c[0];
    h = (h<<8)|c[1];
    h = (h<<8)|c[2];
    h = (h<<8)|c[3];
    h = (h<<8)|c[4];
    h = (h<<8)|c[5];
    h = (h<<8)|c[6];
    h = (h<<8)|c[7];
    return h;
}