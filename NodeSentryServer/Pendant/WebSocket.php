<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2019/1/15
 * Time: 19:47
 */

namespace Pendant;

class WebSocket
{

    static $requestInfo = [];

    static $requestText;

    const BINARY_TYPE_BLOB = "\x81";

    //拆分http响应头
    public static function resolveHttpHeader($request)
    {
        self::$requestText = $request;
        $requestArr = explode("\r\n", $request);
        foreach ($requestArr as $key => $value) {
            $collect = explode(":", $value);
            if (isset($collect[0]) && isset($collect[1])) {
                self::$requestInfo[$collect[0]] = $collect[1];
            }
        }
        return self::$requestInfo;
    }

    //响应http请求
    public static function responseHttpRequest()
    {
        $buf = substr(self::$requestText, strpos(self::$requestText, 'Sec-WebSocket-Key:') + 18);
        $key = trim(substr($buf, 0, strpos($buf, "\r\n")));
        $new_key = base64_encode(sha1($key . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11", true));
        $new_message = "HTTP/1.1 101 Switching Protocols\r\n";
        $new_message .= "Upgrade: websocket\r\n";
        $new_message .= "Sec-WebSocket-Version: 13\r\n";
        $new_message .= "Connection: Upgrade\r\n";
        $new_message .= "Sec-WebSocket-Accept: " . $new_key . "\r\n\r\n";
//        $response = "HTTP/1.1 101 Switching Protocols\r\nUpgrade: websocket\r\nSec-WebSocket-Version: 13\r\nConnection: Upgrade\r\nSec-WebSocket-Accept: %s\r\n\r\n";
//        $httpResponse = sprintf($response,$key);
        return $new_message;
    }

    //解码发送
    public static function decode($received)
    {
        $len = $masks = $data = $decoded = null;
        $buffer = $received;
        if(!$buffer)
        {
            return [
                "data"=>"",
                "buffer"=>$buffer
            ];
        }
        $len = ord($buffer[1]) & 127;

        $position = 0;
        if ($len === 126) {
            $position = 8+$len;
            $masks = substr($buffer, 4, 4);
            $data = substr($buffer, 8,$len);
        } else {
            if ($len === 127) {
                $position = 14+$len;
                $masks = substr($buffer, 10, 4);
                $data = substr($buffer, 14,$len);
            } else {
                $position = 6+$len;
                $masks = substr($buffer, 2, 4);
                $data = substr($buffer, 6,$len);
            }
        }

        //这时一个不完整的包
        if(strlen($received) < $position)
        {
            return [
                "data"=>"",
                "buffer"=>$buffer
            ];
        }

        for ($index = 0; $index < strlen($data); $index++) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }

        //返回剩余的待处理数据
        $last_buffer = substr($received,$position);

        return [
            "data"=>$decoded,
            "buffer"=>$last_buffer
        ];
    }

//    public static function decode($received)
//    {
//        $len = $masks = $data = $decoded = null;
//        $buffer = $received;
//        $len = ord($buffer[1]) & 127;
//        if ($len === 126) {
//            $masks = substr($buffer, 4, 4);
//            $data = substr($buffer, 8,$len);
//        } else {
//            if ($len === 127) {
//                $masks = substr($buffer, 10, 4);
//                $data = substr($buffer, 14,$len);
//            } else {
//                $masks = substr($buffer, 2, 4);
//                $data = substr($buffer, 6,$len);
//            }
//        }
//        for ($index = 0; $index < strlen($data); $index++) {
//            $decoded .= $data[$index] ^ $masks[$index % 4];
//        }
//
//        return $decoded;
//    }

    //编码发送
    public static function encode($buffer)
    {
        $len = strlen($buffer);

        $first_byte = self::BINARY_TYPE_BLOB;

        if ($len <= 125) {
            $encode_buffer = $first_byte . chr($len) . $buffer;
        } else {
            if ($len <= 65535) {
                $encode_buffer = $first_byte . chr(126) . pack("n", $len) . $buffer;
            } else {
                //pack("xxxN", $len)pack函数只处理2的32次方大小的文件，实际上2的32次方已经4G了。
                $encode_buffer = $first_byte . chr(127) . pack("xxxxN", $len) . $buffer;
            }
        }

        return $encode_buffer;
    }

    //对客户端做出响应
    public static function response($data)
    {
//        echo "---------------------------\n";
//        var_dump($data);
        $responseMsg = "recevied data len : ".strlen($data)." $data";
        return $responseMsg;
    }

}