<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-10-31
 * Time: 下午8:30
 */
namespace Pendant\CallEvent;
use Structural\System\OnEventTcpStruct;
use Structural\System\SwooleTcpStruct;
class TcpEvent implements Event {

    //事件对象
    private $eventObject;

    //处理程序举兵
    private $handle;

    public function __construct($eventObject,$handle,...$args)
    {
        $this->eventObject = $eventObject;
        $this->handle = new $handle($args);

    }

    //进程启动前加载常用的类
    public function onWorkerStart()
    {
        if ($this->eventObject instanceof \swoole_server) {
            //在工作进程启动的时候加载实例
            $this->eventObject->on(SwooleTcpStruct::TCP_WorkerStart, [$this->handle,OnEventTcpStruct::ON_bindWorkerStart]);
        }
    }


    //注入接收函数
    public function onReceive()
    {
        if ($this->eventObject instanceof \swoole_server) {
            $this->eventObject->on(SwooleTcpStruct::TCP_Receive, [$this->handle,OnEventTcpStruct::ON_bindReceive]);
        }
    }

    //创建域套接字通讯进程
    public function onTask()
    {
        if ($this->eventObject instanceof \swoole_server) {
            $this->eventObject->on(SwooleTcpStruct::TCP_Task, [$this->handle,OnEventTcpStruct::ON_bindTask]);
        }
    }

    //完成任务处理
    public function onFinish()
    {
        if ($this->eventObject instanceof \swoole_server) {
            $this->eventObject->on(SwooleTcpStruct::TCP_Finish, [$this->handle,OnEventTcpStruct::ON_bindFinish]);
        }
    }



    //关闭套接字的回调函数
    public function onClose()
    {
        if ($this->eventObject instanceof \swoole_server) {
            $this->eventObject->on(SwooleTcpStruct::TCP_Close, [$this->handle, OnEventTcpStruct::ON_bindClose]);
        }
    }

    //接收广播信息
    public function onPipeMessage()
    {
        if ($this->eventObject instanceof \swoole_server) {
            $this->eventObject->on(SwooleTcpStruct::TCP_PipeMessage, [$this->handle, OnEventTcpStruct::ON_bindPipeMessage]);
        }
    }

    public function bindWorkerStop()
    {
        if ($this->eventObject instanceof \swoole_server) {
            $this->eventObject->on(SwooleTcpStruct::TCP_WorkerStop, [$this->handle, OnEventTcpStruct::ON_bindWorkerStop]);
        }
    }

    public function call()
    {
        $this->onWorkerStart();
        $this->onReceive();
        $this->onTask();
        $this->onFinish();
        $this->onClose();
        $this->onPipeMessage();
        $this->bindWorkerStop();
        return true;
    }
}