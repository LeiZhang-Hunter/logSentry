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

    //如果说存在network_url 将会以网络配置为准
    if(!mContent["network_url"]["url"].empty())
    {
        string config_url = mContent["network_url"]["url"];
        string return_val;
        int res = CCurl::httpGet(config_url.c_str(),&return_val);
        if(res == 0)
        {
            CJson jsonTool;
            Json::Value netConfig;
            bool netRet = jsonTool.jsonDecode(return_val,&netConfig);
            if(!netRet)
            {
                LOG_TRACE(LOG_ERROR,false,"CIniFileConfig::readConfig","get ner config error("<<config_url.c_str()<<")");
            }else{
                //清空整个map然后重新插入网络请求下来的数据
                mContent.clear();
                string log_file = netConfig["log_file"]["file_path"].asString();
                //检查日志路径
                if(log_file == "null")
                {
                    printf("[CIniFileConfig::readConfig],get net config[log_file][file_path] error(%s)\n",config_url.c_str());
                    exit(-1);
                }else{
                    mContent["log_file"].insert(map<string,string>::value_type("file_path",log_file));
                }

                //检查哨兵pid文件的配置
                string sentry_pid_file = netConfig["pid_file"].asString();
                if(sentry_pid_file == "null")
                {
                    printf("[CIniFileConfig::readConfig],get net config[pid_file] error(%s)\n",config_url.c_str());
                    exit(-1);
                }else{
                    mContent["sentry"]["pid_file"] = sentry_pid_file;
                }

                //文件哨兵的线程数目
                string file_sentry_thread_number = netConfig["pid_file"].asString();
                if(file_sentry_thread_number == "null")
                {
                    printf("[CIniFileConfig::readConfig],get net config[file_sentry_thread_number] error(%s)\n",config_url.c_str());
                    exit(-1);
                }else{
                    mContent["sentry"]["file_sentry_thread_number"] = file_sentry_thread_number;
                }

                //目录哨兵的线程数目
                string dir_sentry_thread_number =  netConfig["dir_sentry_thread_number"].asString();
                if(dir_sentry_thread_number == "null")
                {
                    printf("[CIniFileConfig::readConfig],get net config[dir_sentry_thread_number] error(%s)\n",config_url.c_str());
                    exit(-1);
                }else{
                    mContent["sentry"]["file_sentry_thread_number"] = dir_sentry_thread_number;
                }

                //检查链接服务器的ip
                string server_ip = netConfig["server"]["ip"].asString();
                if(server_ip == "null")
                {
                    printf("[CIniFileConfig::readConfig],get net config[server][ip] error(%s)\n",config_url.c_str());
                    exit(-1);
                }else{
                    mContent["server"]["ip"] = server_ip;
                }

                string server_port = netConfig["server"]["server_port"].asString();
                if(server_port == "null")
                {
                    printf("[CIniFileConfig::readConfig],get net config[server][port] error(%s)\n",config_url.c_str());
                    exit(-1);
                }else{
                    mContent["server"]["port"] = server_port;
                }

                //获取最大的描述符个数
                string max_fd = netConfig["system"]["max_fd"].asString();
                if(server_port == "null")
                {
                    printf("[CIniFileConfig::readConfig],get net config[system][max_fd] error(%s)\n",config_url.c_str());
                    exit(-1);
                }else{
                    mContent["system"]["max_fd"] = max_fd;
                }

                //循环读取监控的文件
                if(netConfig["sentry_log_file"].isObject())
                {
                    //开始从头遍历，逐个写入mContent这个配置选项
                    Json::Value::Members mem = netConfig["sentry_log_file"].getMemberNames();
                    for(auto iter = mem.begin();iter != mem.end();iter++)
                    {
                        if(netConfig["sentry_log_file"][*iter] != "") {

                            mContent["sentry_log_file"].insert(map<string, string>::value_type(*iter, netConfig["sentry_log_file"][*iter].asString()));
                        }
                    }
                }

                //循环读取监控的目录
                if(netConfig["sentry_log_dir"].isObject())
                {
                    //开始从头遍历，逐个写入mContent这个配置选项
                    Json::Value::Members mem = netConfig["sentry_log_dir"].getMemberNames();
                    for(auto iter = mem.begin();iter != mem.end();iter++)
                    {
                        if(netConfig["sentry_log_dir"][*iter] != "") {

                            mContent["sentry_log_dir"].insert(map<string, string>::value_type(*iter, netConfig["sentry_log_dir"][*iter].asString()));
                        }
                    }
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

    return n;
}