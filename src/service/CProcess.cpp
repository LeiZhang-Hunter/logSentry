//
// Created by zhanglei on 19-8-16.
//
#include "include/MainService.h"
using service::CProcess;

//守护进程
CProcess::CProcess(){
    status = PROCESS_STOP;
    monitorStatus = MONITOR_RUN;
}

//获取进程的pid
pid_t CProcess::getPid() {
    return pid;
}


//创建守护进程
int CProcess::createDaemon()
{
    pid = fork();

    if(pid < 0)
    {
        LOG_TRACE(LOG_ERROR,false,"CProcess::createDaemon","create daemon process error");
        return  -1;
    }else if(pid > 0)
    {
        //杀死掉父进程
        exit(0);
    }

    //如果说设置进程组组长失败，
    if(setsid() < 0)
    {
        LOG_TRACE(LOG_ERROR,false,"CProcess::createDaemon","create process leader error");
        return  -1;
    }


    //这里需要忽略掉信号
    signal(SIGHUP,SIG_IGN);

    pid = fork();

    if(pid < 0)
    {
        return  -1;
    }else if(pid)
    {
        exit(0);
    }

    chdir("/");

    int i;

    for(i=0;i<64;i++)
    {
        close(i);
    }

    //改变描述符指向
    open("/dev/null",O_RDONLY);
    open("/dev/null",O_RDWR);
    open("/dev/null",O_RDWR);

    //运行run函数
    this->run();
    exit(0);
    return  0;
}

//创建进程
int CProcess::createProcess()
{
    pid = fork();

    if(pid<0)
    {
        return  -1;
    }

    if(pid == 0)
    {
        this->run();
        exit(0);
    }

    return 0;
}