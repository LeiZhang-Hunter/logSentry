//
// Created by zhanglei on 2019/11/29.
//

#ifndef LOGSENTRY_CCURL_H
#define LOGSENTRY_CCURL_H
#include "curl/curl.h"
#endif //LOGSENTRY_CCURL_H

using namespace std;
namespace service{
    class CCurl {

    public:
        static int httpGet(const char* url,string returnStr);

    };
}