//
// Created by zhanglei on 19-8-16.
//
#include "MainService.h"
using service::CProcess;

//守护进程
CProcess::CProcess(){
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
        LOG_TRACE(LOG_ERROR,false,"CProcess::createProcess","fork error");
        return  -1;
    }
    if(pid == 0)
    {
        this->run();
        //_exit(0);
        //我们在这里使用的是_exit(0),而没有使用exit（0）,第一版使用的是exit(0)
        //因为exit会触发atexit函数，会导致返回值被掉包
        //子进程的io缓冲被刷出也容易出现问题所以exit换成了_exit(0)
        _exit(0);
    }

    return 0;
}