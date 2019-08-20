//
// Created by zhanglei on 19-8-16.
//
#include "../../include/Common.h"
using app::MainCenter;
using service::SingleInstance;
using std::map;
//运行逻辑
void MainCenter::run() {
    Config* instance = SingleInstance<Config>::getInstance();
    map<string,map<string,string>>mContent = instance->getConfig();
    std::cout<<mContent["server"]["port"]<<"\n";
}

//执行逻辑
void MainCenter::start() {
    this->run();
}