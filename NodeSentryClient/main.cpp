//
// Created by zhanglei on 19-8-9.
//
//#include "include/CThread.h"
#include "Common.h"
using namespace app;

int main(int argc,char** argv)
{
    int opt;
    while ((opt = getopt(argc, argv, "c"))!= -1)
    {
        switch (opt)
        {
            case 'c':

                break;
        }
    }
    std::string path;
    if(argv[optind])
    {
        path = (argv[optind]);
    }else{
        path = (DEFAULT_CONFIG_DIR);
    }
    //运行入口函数创建进程
    MainCenter* main_instance = CSingleInstance<MainCenter>::getInstance();
    main_instance->init(path);
    main_instance->start();
    main_instance->destroy();
    //删除掉主要实例
    delete main_instance;
}
