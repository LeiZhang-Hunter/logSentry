<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/16
 * Time: 14:39
 */
namespace Pendant;

use Library\Logger\Logger;
use Structural\System\ConfigStruct;
use Structural\System\EventStruct;
use Structural\System\SwooleProtocol;

class SwooleSysSocket{

    /**
     * @var SwooleSysSocket
     */
    private static $instance;

    /**
     * @var \swoole_server
     */
    public static $swoole_server;

    /**
     * @var \Closure
     */
    private static $beforeHook;

    /**
     * @var \Closure
     */
    private static $finishHook;

    /**
     * @var SysConfig
     */
    public $config;

    private $ip;

    private $port;

    private $monitor_list = [];

    /**
     * @var Logger
     */
    public $logger;


    public function __construct()
    {
    }

    /**
     * @param Logger $instance
     */
    public function setLogger(Logger $instance)
    {
        $this->logger = $instance;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param $ip
     * @param $port
     * @param $type
     */
    public function addMonitor($ip,$port,$type,$controller)
    {
        $this->monitor_list[] = [
            "ip"=>$ip,
            "port"=>$port,
            "type"=>$type,
            "controller"=>$controller
        ];
    }

    public function regBeforeHook($beforeFunction)
    {
        self::$beforeHook = \Closure::bind($beforeFunction,$this);
    }

    public function regFinishHook($endFunction)
    {
        self::$finishHook = \Closure::bind($endFunction,$this);
    }

    /**
     * Description:获取系统实例
     * @return SwooleSysSocket
     */
    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    //启动服务
    public function run()
    {
        if(is_callable(self::$beforeHook))
        {
            call_user_func_array(self::$beforeHook,[]);
        }
        $sys_factory = new SysFactory();
        //移出主要的server信息
        $server = array_shift($this->monitor_list);
        //监控服务
        self::$swoole_server = new \swoole_server($server[ConfigStruct::S_IP],$server[ConfigStruct::S_PORT],SwooleProtocol::Mode, SwooleProtocol::TCP_PROTOCOL);
        $sys_factory->regServerController($server[ConfigStruct::S_TYPE],$server[ConfigStruct::S_CONTROLLER]);
        //监控其余的端口
        foreach ($this->monitor_list as $monitorInfo) {
            $monitor_type = $monitorInfo[ConfigStruct::S_TYPE];
            $controllerName = $monitorInfo[ConfigStruct::S_CONTROLLER];
            //注册接收的server
            $sys_factory->regServerController($monitor_type,$controllerName);
            //绑定处理事件
            $service_object = self::$swoole_server->addListener($monitorInfo[ConfigStruct::S_IP],$monitorInfo[ConfigStruct::S_PORT],$monitorInfo[ConfigStruct::S_TYPE]);
            EventStruct::bindEvent($monitor_type,$service_object);
        }
        EventStruct::bindEvent($server[ConfigStruct::S_TYPE],self::$swoole_server);
        $sys_factory->setTaskNumber($this->config->getSysConfig()[ConfigStruct::S_TASK_WORKER_NUM]);
        //加入配置文件
        self::$swoole_server->set($this->config->getSysConfig());
        //运行程序
        self::$swoole_server->start();
    }
}