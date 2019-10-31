<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2019/1/8
 * Time: 12:43
 */
namespace Pendant;
class SysAfUnixSocket{
    private $socket;

    private $recieveHook;
    public function create()
    {
        $this->socket = socket_create(AF_UNIX,SOCK_DGRAM,0);
        return $this->socket;
    }

    public function bind($dir)
    {
        unlink($dir);
        $result = socket_bind($this->socket,$dir);
        if(!$result)
        {
            throw new Exception("域套接字绑定失败");
        }
        return $result;
    }

    public function sendTo($dir,$msg)
    {
        return socket_sendto($this->socket,$msg,strlen($msg),0,$dir);
    }


    public function onReceive($function)
    {
        $this->recieveHook = $function;
    }


    public function run()
    {

        for(;;) {
            $read = [$this->socket];
            $write = [];
            $except = [];
            $num = socket_select($read,$write,$except,-1);
            if($num < 0)
            {
                if(pcntl_errno() == PCNTL_EINTR)
                {//慢函数会出现系统信号中断，如果系统信号中断那么可以继续读取
                    continue;
                }
            }
            if($num>0)
            {
                //接收数据包
                $bytes = socket_recvfrom($this->socket,$buf,1204,0,$from,$port);
                if($bytes>0)
                {
                    call_user_func($this->recieveHook,$buf);
                }

            }
        }
    }
}