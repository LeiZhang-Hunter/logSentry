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
    }

    public function onReceive($content)
    {
        //监控发送数据类型
        $monitor_type = $content[LogSentryStruct::Monitor_type];


        $buffer = $content[LogSentryStruct::Buf_body];

        //上报事件
        $tick_time = $content[LogSentryStruct::Time];

        $data = [];

        $save_file = "";

        //日志哨兵类型
        $sentry_type = 0;

        switch ($monitor_type)
        {
            //监控的类型是文件
            case LogSentryStruct::Monitor_file:
                $monitor_file = $content[LogSentryStruct::File_name];
                $sentry_type = 0;
                $data[LogSentryStruct::Monitor_type] = LogSentryStruct::Monitor_file;
                $data = call_user_func_array([$this->protocolHandle[$monitor_type][$monitor_file],LogProtocol::Parse],[$buffer]);
                $save_file = $monitor_file;
                break;
            case LogSentryStruct::Monitor_dir:
                $monitor_dir = $content[LogSentryStruct::Dir_name];
                $sentry_type = 1;
                $data[LogSentryStruct::Monitor_type] = LogSentryStruct::Monitor_dir;
                $data = call_user_func_array([$this->protocolHandle[$monitor_type][$monitor_dir],LogProtocol::Parse],[$buffer]);
                $save_file = $monitor_dir;
                break;
        }

        if(!$data)
        {
            $this->logger->trace(Logger::LOG_WARING,self::class,"onReceive","buffer type [$monitor_type] error");
            return false;
        }

        if(!$save_file)
        {
            $this->logger->trace(Logger::LOG_WARING,self::class,"onReceive","log file [$save_file] error");
            return false;
        }


        //循环内容
        foreach ($data as $dataUnit)
        {
            if($dataUnit) {
                /**
                 * 过滤php错误级别
                 */
                foreach (PHPErrorLog::$php_error as $error_key=>$error_value)
                {
                    if(strpos($dataUnit,($error_value)) !== false)
                    {
                        $logUnit = [];
                        $logUnit[LogDbStruct::Sentry_type] = $sentry_type;
                        $logUnit[LogDbStruct::Sentry_file] = md5($save_file);//md5编码方便查询
                        $logUnit[LogDbStruct::Client_ip] = $content[LogDbStruct::Client_ip];
                        $logUnit[LogDbStruct::Happen_time] = date("Y-m-d H:i",$tick_time);
                        $logUnit[LogDbStruct::Body] = htmlspecialchars(addslashes($dataUnit));//内容
                        $logUnit[LogDbStruct::Php_error_level] = $error_key;//php报错级别
                        $logUnit[LogDbStruct::Created_time] = time();
                        $logUnit[LogDbStruct::State] = 1;//正常状态
                        $logUnit[LogDbStruct::Type] = 1;//级别为php日志
                        if(($res = $this->db->insert("sys_syslog",$logUnit)) !== false){

                            //获取最后插入的id 然后放入es 中这里是原子操作不需要担心安全问题
                            $insertId = $this->db->getLastInsertId();
                            $logUnit["sys_id"] = (int)$insertId;
                            //存入es
                            if(($res = $this->es->client->index($logUnit,$insertId)))
                            {
                                //如果说es写入失败记录下日志
                            }

                        }else{
                            $this->logger->trace(Logger::LOG_WARING,"LogSentryController","onReceive","mysql error:".$this->db->getLastError().";data:".json_encode($logUnit));
                        }


                        break;
                    }

                }
            }
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