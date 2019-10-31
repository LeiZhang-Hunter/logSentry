//
// Created by prince on 9/20/19.
//
#include "MainService.h"
using namespace service;
CServiceLog* logInstance;
int main()
{
    const char* dir="/home/prince/code/c/logSentry/Log/log.log";

    logInstance =  new CServiceLog(dir);//初始化日志文件
    LOG_TRACE(LOG_ERROR, false,"MainCenter::run"," test 1 ");
    //    cServiceLog.addLog()
    return 0;
}
