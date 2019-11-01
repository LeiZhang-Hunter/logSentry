<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-9-18
 * Time: 下午7:39
 */
namespace Controller;
use Library\LogProtocol\PHPErrorLog;
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
    }

    public function onReceive($content)
    {
        //监控发送数据类型
        $monitor_type = $content[LogSentryStruct::Monitor_type];

        $monitor_file = $content[LogSentryStruct::File_name];

        $buffer = $content[LogSentryStruct::Buf_body];

        //上报事件
        $tick_time = $content[LogSentryStruct::Time];

        $data = [];
        switch ($monitor_type)
        {
            //监控的类型是文件
            case LogSentryStruct::Monitor_file:
                $data = call_user_func_array([$this->protocolHandle[$monitor_type][$monitor_file],LogProtocol::Parse],[$buffer]);
                break;
        }

        if(!$data)
        {
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
                    //如果说存在php的错误级别
                    if(strpos($dataUnit,($error_value)) !== false)
                    {
                        $logUnit = [];
                        //压缩
                        $logUnit[LogDbStruct::Body] = addslashes($dataUnit);
//                        $logUnit[LogDbStruct::File_name] = $monitor_file;
                        $logUnit[LogDbStruct::Happen_time] = date("Y-m-d H:i",$tick_time);
                        $logUnit[LogDbStruct::Created_time] = time();
                        $logUnit[LogDbStruct::Project_id] = 0;
                        $logUnit[LogDbStruct::State] = 1;
                        $logUnit[LogDbStruct::Type] = 1;
                        $logUnit[LogDbStruct::Php_error_level] = $error_key;
                        $logUnit[LogDbStruct::Level] = 0;
                        if(($res = $this->db->insert("sys_syslog",$logUnit))){
                            //获取最后插入的id 然后放入es 中这里是原子操作不需要担心安全问题
                            $insertId = $this->db->getLastInsertId();
                            $data["sys_id"] = (int)$insertId;
                            //存入es
                            if(!$this->es->client->index($data,$insertId))
                            {
                            }

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