//
// Created by zhanglei on 19-8-12.
//
#include "../../include/MainService.h"

//设置配置文件加载的路径
bool service::Config::setPath(std::string& path) {
    configPath = path;
    return true;
}

//获取配置选项
std::string service::Config::getConfig(){

}

//加载配置文件中的配置放入到内存中
bool service::Config::loadConfig(){
    std::map<string,string> mContent;
    SingleInstance<IniFileConfig>::getInstance()->readConfig(configPath,mContent);
}

//获取路径
std::string service::Config::getPath(){
    return configPath;
}