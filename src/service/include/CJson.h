//
// Created by zhanglei on 19-10-18.
//

#ifndef LOGSENTRY_CJSON_H
#define LOGSENTRY_CJSON_H

#include "json/json.h"
using namespace Json;
namespace service {
    class CJson {
    public:
        CJson();
        ~CJson();
        static const char* jsonDecode();
        String jsonEncode(Value proto_value);
        void release();
        char* jsonBuffer;
    };
}


#endif //LOGSENTRY_CJSON_H
