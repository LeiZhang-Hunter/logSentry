//
// Created by zhanglei on 19-8-14.
//
#include "MainService.h"
using namespace std;
//读取配置文件
bool service::CIniFileConfig::readConfig(string &filename) {

    if (access(filename.c_str(), R_OK) == -1) {
        cout<<"log file"<<filename.c_str()<<" is not exist;file:"<<__FILE__<<";line:"<<__LINE__;
        exit(-1);
    }

    //打开配置文件进行读取
    fileFd = open(filename.c_str(), O_RDWR);

    if (!fileFd) {
        cout<<"open log failed:"<<__FILE__<<";line:"<<__LINE__;
        exit(-1);
    }

    //开始循环一个一个字节的读取配置文件,加载入map中
    char buf[MAXLINE];
    string config_buffer;
    string section;
    size_t len;
    struct unit un;
    while ((readLine(fileFd, buf, 1024 * 8))) {
        len = strlen(buf);

        if(len>0)
        {
            config_buffer = buf;
            //#号开头代表注释
            if(config_buffer[0] != '#' && config_buffer[0] != ';') {
                if (config_buffer.find('[') == 0 && config_buffer.find(']') == len - 1) {
                    //解析出他的值
                    section = config_buffer.substr(1, len - 2);
                } else if (config_buffer.find('=') != string::npos) {

                    un.key = config_buffer.substr(0, config_buffer.find('='));
                    un.value = config_buffer.substr(config_buffer.find('=') + 1);
                    mContent[section].insert(map<string,  string>::value_type(un.key,un.value));
                }
            }
        }
    }

    this->onGetConfig(mContent);
}

int service::CIniFileConfig::onGetConfig(map<string,map <string,string>>Config) {

}


//按照行来读取
ssize_t service::CIniFileConfig::readLine(int fd, char *buf, size_t maxLine) {
    bzero(buf, maxLine);
    ssize_t n;
    n = 0;
    char c;
    ssize_t res;

    while ((res = read(fd, &c, 1))) {
        if (res == -1) {

            //如果说被信号中断那么就要继续运行不要停
            if(errno == EINTR)
            {
                continue;
            }

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