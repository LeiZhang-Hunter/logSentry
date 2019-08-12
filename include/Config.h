//
// Created by zhanglei on 19-8-12.
//

#include <string>

#ifndef SOCKETSENTRY_CONFIG_H
#define SOCKETSENTRY_CONFIG_H

#endif //SOCKETSENTRY_CONFIG_H

namespace service{
    class Config{
    public:
        bool setPath(std::string path);
        std::string getPath();
        bool loadConfig();
        std::string getConfig();

    private:
        std::string* configPath;
    };
}
