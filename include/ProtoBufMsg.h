//
// Created by zhanglei on 19-9-17.
//

#ifndef LOGSENTRY_PROTOBUF_H
#define LOGSENTRY_PROTOBUF_H

#endif //LOGSENTRY_PROTOBUF_H

typedef struct _protoHeader
{
    uint8_t version;//版本号
    uint8_t magic;//协议的魔数
    uint8_t server;
    uint64_t body_len;//协议体的长度
    char buf[];//报文主体内容
}protoHeader;

/**__________________________________________________________________
 * |              |                        |                        |
 * |    包的长度   |     包头                |        包体            |
 * | (uint32_t)   | （protoHeader）         |        char(文本)      |
 * |______________|________________________|________________________|
 */

class ProtoBufMsg{
public:
    ProtoBufMsg()
    {

    }
    //压缩消息头
    size_t* encodeProtoStruct(const char* json_buffer);
    size_t getProtoLen();

private:
    size_t protoLen;
};