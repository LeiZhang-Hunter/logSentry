//
// Created by zhanglei on 19-10-18.
//

#include "MainService.h"

using namespace service;

bool CJson::jsonDecode(string strJsonMess,Json::Value* root) {
    Json::CharReaderBuilder readerBuilder;
    std::unique_ptr<Json::CharReader>  jsonReader(readerBuilder.newCharReader());
    JSONCPP_STRING errs;
    Json::Value parseValue;
    bool res = jsonReader->parse(strJsonMess.c_str(),strJsonMess.c_str()+strJsonMess.length(),root,&errs);
    if (!res || !errs.empty()) {
        LOG_TRACE(LOG_ERROR,false,"CJson::jsonDecode","jsonDecode error,error msg:"<<errs);
        return false;
    }else{
        //转化为map
        return true;
    }
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

