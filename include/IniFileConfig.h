//
// Created by zhanglei on 19-8-14.
//


#ifndef LOGSENTRY_INIFILECONFIG_H
#define LOGSENTRY_INIFILECONFIG_H

using std::map;
using std::string;

#endif //LOGSENTRY_INIFILECONFIG_H
namespace service {
    class IniFileConfig {
    public:
        bool readConfig(std::string &filename, map<string, string> &Config);

        ssize_t readLine(int fd,char* buf,size_t manxLine);


    private:
        int fileFd;
    };
}