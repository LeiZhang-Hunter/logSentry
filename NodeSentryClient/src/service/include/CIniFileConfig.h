//
// Created by zhanglei on 19-8-14.
//


#ifndef LOGSENTRY_CINIFILECONFIG_H
#define LOGSENTRY_CINIFILECONFIG_H

using std::map;
using std::string;
using std::iterator;

#define MAXLINE 1024 * 8

#endif //LOGSENTRY_INIFILECONFIG_H


namespace service {
    class CIniFileConfig {
    public:

        struct unit{
            string key;
            string value;
        };



        map<string,map <string,string>>mContent;

        bool readConfig(std::string &filename);

        ssize_t readLine(int fd,char* buf,size_t manxLine);

        virtual int onGetConfig(map<string,map <string,string>>Config);


    private:
        int fileFd;
    };
}