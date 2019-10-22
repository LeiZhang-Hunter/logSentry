//
// Created by zhanglei on 19-9-17.
//

#include "Common.h"

//压缩协议头
//bool ProtoBufMsg::encodeHeader() {
//
//}

//压缩协议
size_t* ProtoBufMsg::encodeProtoStruct(const char* json_buffer) {
    protoHeader header;
    size_t header_len;//包头的长度
    size_t body_len;//包体的长度

    //包头的长度
    header_len = sizeof(header_len);
    //计算消息体的长度
    body_len = strlen(json_buffer)+1;
    protoLen = sizeof(size_t)+sizeof(protoHeader)+body_len;

    //申请内存地址
    auto proto_addr = (size_t *)malloc(protoLen);

    auto save_ptr = proto_addr;

    //初始化内存，将申请内容里面的值全部初始化为0
    bzero(proto_addr,protoLen);

    *proto_addr = os->htonll(protoLen);
    proto_addr++;
    header.version = SENTRY_VERSION;
    header.server = 0;
    header.magic = MAGIC;
    header.body_len = os->htonll(body_len);

    auto dataStruct = (protoHeader*)proto_addr;
    //包头的长度
    memcpy(dataStruct,&header,header_len);
    dataStruct++;

    memcpy(dataStruct,json_buffer,body_len);
    return save_ptr;
}



size_t ProtoBufMsg::getProtoLen(){
    return protoLen;
}