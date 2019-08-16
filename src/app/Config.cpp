//
// Created by zhanglei on 19-8-12.
//
#include "../../include/Common.h"

//设置配置文件加载的路径
using service::SingleInstance;
bool app::Config::setPath(std::string& path) {
    configPath = path;
    return true;
}

//获取配置选项
std::string app::Config::getConfig(){

}

//加载配置文件中的配置放入到内存中
bool app::Config::loadConfig(){
    SingleInstance<IniFileConfig>::getInstance()->readConfig(configPath);
}

//获取路径
std::string app::Config::getPath(){
    return configPath;
}

int app::Config::onGetConfig(char *buf) {
    printf("%s\n",buf);
}