//
// Created by zhanglei on 19-8-14.
//
#include "../../include/MainService.h"

//读取配置文件
bool service::IniFileConfig::readConfig(std::string &filename, map<string, string> &Config) {

    if (access(filename.c_str(), R_OK) == -1) {
        LOG_TRACE(LOG_WARING, false, "Log",
                  "IniFileConfig::readConfig" << __LINE__ << ":" << filename << " is not file;");
        return false;
    }

    //打开配置文件进行读取
    fileFd = open(filename.c_str(), O_RDWR);

    if (!fileFd) {
        LOG_TRACE(LOG_WARING, false, "Log",
                  "IniFileConfig::readConfig" << __LINE__ << ":" << filename << " open failed;");
        return false;
    }

    //开始循环一个一个字节的读取配置文件,加载入map中
    char buf[1024 * 8];
    ssize_t n;
    while ((n = readLine(fileFd, buf, 1024 * 8))) {
        printf("%s\n", buf);
    }
}


//按照行来读取
ssize_t service::IniFileConfig::readLine(int fd, char *buf, size_t maxLine) {
    bzero(buf, maxLine);
    ssize_t n;
    n = 0;
    char c;
    ssize_t res;

    while ((res = read(fd, &c, 1))) {
        if (res == -1) {
            return -1;
        }
        n++;
        if (c == '\n') {
            return n;
        }
        *buf++ = c;
        //到达行数的最大值了再进行累加就要越界了
        if (n == maxLine) {
            return n;
        }
    }

    return 0;
}