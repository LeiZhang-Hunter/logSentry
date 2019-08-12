//
// Created by zhanglei on 19-8-12.
//
#include <iostream>
#include <string>
#include "../../include/Config.h"

//设置配置文件加载的路径
bool service::Config::setPath(std::string path) {
    configPath = &path;
}

//获取配置选项
std::string service::Config::getConfig(){

}

//加载配置文件中的配置放入到内存中
bool service::Config::loadConfig(){

}

//获取路径
std::string service::Config::getPath(){

}