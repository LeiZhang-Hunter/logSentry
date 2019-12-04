//
// Created by zhanglei on 19-10-18.
//

#include "MainService.h"

using namespace service;

Value CJson::jsonDecode(string strJsonMess) {
//    Json::Reader jsonReader;
//    Json::Value parseValue;
//    jsonReader.parse(strJsonMess,parseValue);
}

//压缩数据
String CJson::jsonEncode(Value proto_value){
    StreamWriterBuilder proto_writer;
    //默认不格式化
    proto_writer.settings_["indentation"] = "";
    string json_string;
    String serialize_string = writeString(proto_writer,proto_value);
    json_string = serialize_string;
    return json_string;
}

