//
// Created by zhanglei on 19-8-12.
//
#include "Common.h"

//设置配置文件加载的路径
using std::string;
using std::map;
bool Config::setPath(std::string& path) {
    configPath = path;
    return true;
}

//获取配置选项
map<string,map<string,string>> Config::getConfig(){
    return iniConfig;
}

//加载配置文件中的配置放入到内存中
bool Config::loadConfig(){
    this->readConfig(configPath);
}

//获取路径
std::string Config::getPath(){
    return configPath;
}

int Config::onGetConfig(map<string,map <string,string>>ConfigData){
    iniConfig = ConfigData;
}