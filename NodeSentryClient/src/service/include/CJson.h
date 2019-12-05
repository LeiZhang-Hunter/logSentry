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
        bool jsonDecode(string strJsonMess,Json::Value* root);
        String jsonEncode(Value proto_value);
    };
}


#endif //LOGSENTRY_CJSON_H
