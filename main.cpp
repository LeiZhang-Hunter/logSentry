//
// Created by zhanglei on 19-8-9.
//
//#include "include/CThread.h"
#include <iostream>
#include "include/Config.h"
using service::Config;
int main()
{
    //从配置文件中读取要监控的项目位置
    Config* instance = new service::Config();
    std::string path("/home/zhanglei/ourc/test/config/config.ini");
    instance->getPath();
    std::cout<<instance->getPath()<<"\n";
}