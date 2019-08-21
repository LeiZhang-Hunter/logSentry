//
// Created by zhanglei on 19-8-16.
//
#include "../../include/Common.h"
using app::MainCenter;
using service::SingleInstance;
using namespace std;
//运行逻辑

//执行逻辑
void MainCenter::start() {
    Config* instance = SingleInstance<Config>::getInstance();
    map<string,map<string,string>>mContent = instance->getConfig();

    map<string,string> file_collect = mContent["sentry_log_file"];
    map<string,string>::iterator it;
    if(!file_collect.empty())
    {
        for(it=file_collect.begin();it != file_collect.end();it++)
        {
            cout << it->first <<"\t"<<file_collect[it->first] << endl;
        }

    }else{
        LOG_TRACE(LOG_ERROR,false,"MainCenter::run",__LINE__.":The log option in the configuration file does not exist");
    }
}