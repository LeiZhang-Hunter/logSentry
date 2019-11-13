<?php
/**
 * Description:拆解syslog 协议
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/16
 * Time: 16:18
 */
namespace Pendant\Protocol\Tcp;
use Library\Logger\Logger;
use Pendant\ProtoInterface\ProtoServer;
use Pendant\SwooleSysSocket;
use Pendant\SysFactory;
use Structural\System\ConfigStruct;
use Structural\System\EventStruct;
use Structural\System\OnEventTcpStruct;
use Structural\System\SwooleProtocol;

class TcpProtocol implements ProtoServer{

    const protocol_type = SwooleProtocol::TCP_PROTOCOL;

    private static $packBuffer;

    const F_fileName = "fileName";

    const F_msg = "msg",F_happen_time = "happen_time";


    //最大的包头
    const UNPACK_HEADERLEN = "Jlength";

    const MAX_PACK_HEADER = 1024*10;//最大10M数据了不能再收了

    const MIN_PACK_HEADER = 16;

    const PACK_HEADER_STRUCT = "c1version/c1magic/c1server";

    const MAGIC = 103;

    private $buffer;

    public static $data;

    //控制器
    private $controller;

    /**
     * @var Logger
     */
    private $logger;




    public function __construct()
    {

        $this->controller = SysFactory::getInstance()->getServerController(self::protocol_type);
        $this->logger = SwooleSysSocket::getInstance()->getLogger();
    }



    public function bindWorkerStart(...$args)
    {
        $server = $args[0];
        $worker_id = $args[1];
        if($worker_id >= $server->setting[ConfigStruct::S_WORKER_NUM]) {
            $callfunc = [$this->controller,EventStruct::OnWorkerStart];
            if(is_callable($callfunc)) {

                //从配置文件中获取实例的静态
                call_user_func_array($callfunc, [$server]);
            }else{
                $this->logger->trace(Logger::LOG_ERROR,self::class,OnEventTcpStruct::ON_bindWorkerStart,"[controller[".self::protocol_type."]->".EventStruct::OnWorkerStart."] is not callable");
            }
        }

    }

    public function bindTask(...$args)
    {
        $server = $args[0];
        $task_id = $args[1];
        $from_id = $args[2];
        $data = $args[3];

        $callfunc = [$this->controller,EventStruct::OnReceive];
        if(is_callable($callfunc)) {

            //从配置文件中获取实例的静态
            call_user_func_array($callfunc, [$data]);
        }else{
            $this->logger->trace(Logger::LOG_ERROR,self::class,OnEventTcpStruct::ON_bindTask,"[controller[".self::protocol_type."]->".EventStruct::OnReceive."] is not callable");
        }

    }

    private function closeClient($fd)
    {
        $fdinfo = SwooleSysSocket::$swoole_server->getClientInfo($fd);
        SwooleSysSocket::$swoole_server->close($fd);
        $this->buffer[$fd] = "";
        $this->logger->trace(Logger::LOG_WARING,self::class,"closeClient","[".self::class."->"."closeClient"."] is closed;remote ip:".$fdinfo["remote_ip"].";remote port:".$fdinfo["remote_port"]);
        return true;
    }



    public function bindReceive(...$args)
    {
        $data = $args[3];
        $fd = $args[1];
        $server = $args[0];

        //如果说在套接字缓冲区里有数据
        if(isset($this->buffer[$fd]))
        {
            $data = $this->buffer[$fd].$data;
        }

        //计算出整个包的长度
        $dataLen = strlen($data);


        $leftLen = $dataLen;//没开始解包之前剩余的数据就是收到包的长度
        $packData = $data;//初始包就是接收的整个包
        $readLen = 0;//读过的包长是0

        //如果说剩余的长度大于0
        while($leftLen > 0)
        {

            //包不完整出现了半包直接放入到缓冲区中
            if($leftLen < 8)
            {
                $this->buffer[$fd] = $packData;
                $this->logger->trace(Logger::LOG_WARING,self::class,"bindReceive","[".self::class."->"."bindReceive"."] recv bytes is small;len:$dataLen");
                return true;
            }

            //解析包头算出整个包的长度
            $packLenArray = unpack(self::UNPACK_HEADERLEN,substr($packData,0,8));
            if(!$packLenArray)
            {
                //关闭掉这个描述符,解析包的长度错误
                $this->closeClient($fd);
                return false;
            }

            //计算出整个包的长度
            $packLen = $packLenArray["length"];

            //检查包的长度,包长超了最大包长 或者包的长度小于最小包长 就会认为这个包已经坏掉了
            if($packLen>self::MAX_PACK_HEADER && $packLen<self::MIN_PACK_HEADER)
            {
                //关闭掉这个描述符,解析包的长度错误
                $this->closeClient($fd);
                return false;
            }

            //半包直接放入缓冲区中
            if($leftLen < $packLen)
            {
                $this->buffer[$fd] = $packData;
                return true;
            }


            //截取这个包的包体 (版本号 模数 服务号这些信息)
            $packHeaderBody = unpack(self::PACK_HEADER_STRUCT,substr($packData,8,8));

            //解析包体长度如果说错误那么就关闭掉链接
            if(!$packHeaderBody)
            {
                $this->closeClient($fd);
                return false;
            }

            $version = $packHeaderBody["version"];//版本号
            $magic = $packHeaderBody["magic"];//模数
            $serverId = $packHeaderBody["server"];//服务号

            //进行模数校验
            if($magic != self::MAGIC)
            {
                $this->closeClient($fd);
                return false;
            }

            //获取这个包体的长度
            $body_len_struct = unpack(self::UNPACK_HEADERLEN,substr($packData,16,8));
            if(!$body_len_struct)
            {
                $this->closeClient($fd);
                return false;
            }

            //解析得到了这个包体的长度
            $body_len = $body_len_struct["length"];


            $body = substr($data,24,$body_len-1);

            //解析body，去掉尾部的\0
            if(strlen($body) == $body_len-1)
            {
                //解析buffer数据
                $buffer = json_decode($body,1);

                if($buffer) {
                    //将buffer下方到task
                    $task_worker_id = SysFactory::getInstance()->getTaskWorkerNumber();
                    //获取客户端的ip并且放入结果集
                    $fdInfo = SwooleSysSocket::$swoole_server->getClientInfo($fd);
                    $buffer["client_ip"] = $fdInfo["remote_ip"];
                    SwooleSysSocket::$swoole_server->task($buffer, $fd % $task_worker_id);
                }
            }


            $readLen+=$packLen;

            //除掉一个包的长度因为一个包已经解包完成了
            $leftLen = $dataLen - $readLen;

            $packData = substr($data,$readLen,$leftLen);
        }

        return;
    }

    public function bindWorkerStop()
    {

    }

    public function bindPipeMessage(...$args)
    {

    }

    public function bindFinish(...$args)
    {

    }

    public function bindClose(...$args)
    {

    }

}