//
// Created by zhanglei on 19-8-9.
//
//#include "include/CThread.h"
#include "include/MainService.h"
using service::Config;
int main(int argc,char** argv)
{
    //解析命令行参数,获取配置文件路径
    Config* instance = new service::Config();
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
}
