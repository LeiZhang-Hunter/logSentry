//
// Created by zhanglei on 19-8-9.
//
//#include "include/CThread.h"
#include "include/Common.h"
using app::Config;
using service::SingleInstance;
using app::MainCenter;
int main(int argc,char** argv)
{
    //解析命令行参数,获取配置文件路径
    Config* instance = SingleInstance<Config>::getInstance();
    if(!instance)
    {
        exit(-1);
    }

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

    instance->setPath(path);

    //如果说存在
    if(instance->getPath().c_str())
    {
        instance->loadConfig();
    }else{
        exit(-1);
    }
    //运行入口函数创建进程
    MainCenter* main_instance = SingleInstance<MainCenter>::getInstance();
    main_instance->start();
}
