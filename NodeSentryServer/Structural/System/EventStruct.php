<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-10-31
 * Time: 下午8:35
 */
namespace Structural\System;
use Pendant\CallEvent\TcpEvent;
use Pendant\Protocol\Tcp\TcpProtocol;

class EventStruct{

    const Event = "event";

    const Call = "call";

    const OnReceive = "onReceive";

    const OnWorkerStart = "onWorkStart";

    public static $collect = [
        SwooleProtocol::TCP_PROTOCOL=>[
            self::Event=>TcpEvent::class,
            self::Call=>TcpProtocol::class
        ]
    ];

    //获取回调实例
    public static function getCall($protocol)
    {
        return isset(self::$collect[$protocol][self::Call]) ? self::$collect[$protocol][self::Call] : null;
    }

    //获取事件
    public static function getEvent($protocol)
    {
        return isset(self::$collect[$protocol][self::Event]) ? self::$collect[$protocol][self::Event] : null;
    }

    //绑定处理事件
    public static function bindEvent($protocol,$object)
    {
        if(is_object($object))
        {
            $eventName = self::getEvent($protocol);
            $bindReactorObject = new $eventName($object,self::getCall($protocol));
            $bindReactorObject->call();
            unset($bindReactorObject);
            return true;
        }
        return;
    }
}