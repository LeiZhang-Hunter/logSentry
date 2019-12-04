<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-9-18
 * Time: 下午7:39
 */
namespace Controller;
use Library\Logger\Logger;
use Library\LogProtocol\PHPErrorLog;
use Pendant\SwooleSysSocket;
use Pendant\SysConfig;
use Structural\System\ConfigStruct;
use Structural\System\LogDbStruct;
use Structural\System\LogProtocol;
use Structural\System\LogSentryStruct;
use Vendor\DB;
use Vendor\ES;

class LogSentryController{

    //协议处理函数
    private $protocolHandle;

    /**数据库实例
     * @var DB
     */
    private $db;

    /**ElasticSearch实例
     * @var ES
     */
    private $es;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * 协议类型
     * LogSentryController constructor.
     * @param $protocol
     */
    public function __construct($protocol)
    {
        //注入的对应文件处理协议
        $this->protocolHandle = isset(SysConfig::getInstance()->getSysConfig()[ConfigStruct::S_FILE_PRO_OBJECT][$protocol]) ? SysConfig::getInstance()->getSysConfig()[ConfigStruct::S_FILE_PRO_OBJECT][$protocol] : null;

    }


    //初始化一些必要操作
    public function onWorkerStart($server)
    {
        $this->db = new DB(SysConfig::getInstance()->getSysConfig("db"));
        $this->es = new ES(SysConfig::getInstance()->getSysConfig("es"));
        $this->logger = SwooleSysSocket::getInstance()->getLogger();

        //循环触发钩子
        foreach ($this->protocolHandle as $protocolParseList)
        {
            foreach ($protocolParseList as $protocol)
            {
                call_user_func_array([$protocol,"ModuleInit"],[$this->logger,$this->db,$this->es]);
            }
        }
    }

    //收到数据的时候触发
    public function onReceive($content)
    {
        //监控发送数据类型
        $monitor_type = $content[LogSentryStruct::Monitor_type];

        switch ($monitor_type)
        {
            //监控的类型是文件
            case LogSentryStruct::Monitor_file:
                $monitor_file = $content[LogSentryStruct::File_name];
                $sentry_type = 0;
                $data[LogSentryStruct::Monitor_type] = LogSentryStruct::Monitor_file;
                if(!isset($this->protocolHandle[$monitor_type][$monitor_file]["handle"]))
                {
                    $this->logger->trace(Logger::LOG_WARING,"LogSentryController","onReceive","this->protocolHandle[$monitor_type][$monitor_file] not set");
                    break;
                }

                $split = isset($this->protocolHandle[$monitor_type][$monitor_file]["handle"]) ? $this->protocolHandle[$monitor_type][$monitor_file]["handle"] : PHP_EOL;
                call_user_func_array([$this->protocolHandle[$monitor_type][$monitor_file]["handle"], LogProtocol::Parse], [$content, $sentry_type,$split]);
                break;
            case LogSentryStruct::Monitor_dir:
                $monitor_dir = $content[LogSentryStruct::Dir_name];
                $sentry_type = 1;
                $data[LogSentryStruct::Monitor_type] = LogSentryStruct::Monitor_dir;
                if(!isset($this->protocolHandle[$monitor_type][$monitor_dir]["handle"]))
                {
                    $this->logger->trace(Logger::LOG_WARING,"LogSentryController","onReceive","this->protocolHandle[$monitor_type][$monitor_dir] not set");
                    break;
                }
                $split = isset($this->protocolHandle[$monitor_type][$monitor_dir]["handle"]) ? $this->protocolHandle[$monitor_type][$monitor_dir]["handle"] : PHP_EOL;
                call_user_func_array([$this->protocolHandle[$monitor_type][$monitor_dir]["handle"], LogProtocol::Parse], [$content, $sentry_type,$split]);
                break;
        }
        return;
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