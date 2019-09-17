//
// Created by zhanglei on 19-9-17.
//

#ifndef LOGSENTRY_PROTOBUF_H
#define LOGSENTRY_PROTOBUF_H

#endif //LOGSENTRY_PROTOBUF_H

typedef struct _protoHeader
{

};

class ProtoBuf{
    //压缩消息头
    bool encodeHeader();
    //压缩协议
    bool encodeProto();
};