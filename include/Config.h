//
// Created by zhanglei on 19-8-12.
//

#include <string>

#ifndef SOCKETSENTRY_CONFIG_H
#define SOCKETSENTRY_CONFIG_H

#endif //SOCKETSENTRY_CONFIG_H

#define DEFAULT_CONFIG_DIR "/home/zhanglei/ourc/logSentry/config/config.ini"
using service::IniFileConfig;
namespace app{
    class Config :public IniFileConfig{
    public:
        bool setPath(std::string& path);
        std::string getPath();
        //加载配置
        bool loadConfig();
        virtual int onGetConfig(map<string,map <string,string>>ConfigData);
        std::string getConfig();

    private:
        std::string configPath;
        map<string,map <string,string>>iniConfig;
    };
}
