//
// Created by zhanglei on 19-10-18.
//

#ifndef LOGSENTRY_CJSON_H
#define LOGSENTRY_CJSON_H

#include "json/json.h"
namespace service {
    class CJson {
    public:
        static const char* jsonDecode();
        static const char* jsonEncode();

    };
}


#endif //LOGSENTRY_CJSON_H
