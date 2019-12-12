<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-10-31
 * Time: 下午7:38
 */

namespace Library\LogProtocol;

use Library\Logger\Logger;
use Structural\System\LogDbStruct;
use Structural\System\LogSentryStruct;
use Vendor\DB;
use Vendor\ES;

//根据传入的切割符来切割的类
class SplitLog implements ResolveProtocol {

    /**
     * @var Logger
     */
    private static $logger;

    /**
     * @var DB
     */
    private static $db;

    /**
     * @var ES
     */
    private static $es;

    public static function ModuleInit($logger,$db,$es)
    {
        self::$logger = $logger;
        self::$db = $db;
        self::$es = $es;
    }

    //参数解析
    public static function parse($bufferInfo,$sentry_type,$split="")
    {

        $buffer = $bufferInfo[LogSentryStruct::Buf_body];
        $data = array_filter(explode($split,$buffer));

        if(!$data)
        {
            self::$logger->trace(Logger::LOG_WARING,self::class,"onReceive","buffer type [$sentry_type] error");
            return false;
        }

        $monitor_file = $bufferInfo[LogSentryStruct::Monitor_log_dir];

        if(!$monitor_file)
        {
            self::$logger->trace(Logger::LOG_WARING,self::class,"onReceive","log file [$monitor_file] error");
            return false;
        }


        //循环内容
        foreach ($data as $dataUnit)
        {
            if($dataUnit) {

                $logUnit = [];
                $logUnit[LogDbStruct::Sentry_type] = $sentry_type;
                $logUnit[LogDbStruct::Sentry_file] = md5($monitor_file);//md5编码方便查询
                $logUnit[LogDbStruct::Client_ip] = $bufferInfo[LogDbStruct::Client_ip];
                $logUnit[LogDbStruct::Happen_time] = $bufferInfo[LogSentryStruct::Time];
                $logUnit[LogDbStruct::Body] = htmlspecialchars(addslashes($dataUnit));//内容
                $logUnit[LogDbStruct::Php_error_level] = 0;//php报错级别
                $logUnit[LogDbStruct::Body_token] = md5($dataUnit);
                $logUnit[LogDbStruct::Created_time] = time();
                $logUnit[LogDbStruct::State] = 1;//正常状态
                $logUnit[LogDbStruct::Type] = 0;//级别为php日志

                //确确实实没有这个日志记录
                if(($res = self::$db->insert("sys_syslog",$logUnit)) !== false){
                    //获取最后插入的id 然后放入es 中这里是原子操作不需要担心安全问题
                    $insertId = self::$db->getLastInsertId();
                    $logUnit["sys_id"] = (int)$insertId;
                    //存入es
                    if(($res = self::$es->client->index($logUnit,$insertId)))
                    {
                        //如果说es写入失败记录下日志
                        if(isset($res["error"]))
                        {
                            $msg = isset($res["error"]["reason"]) ? $res["error"]["reason"] : "";
                            self::$logger->trace(Logger::LOG_ERROR,"PHPErrorLog","parse","elasticSearch error:".$msg.";data:".json_encode($logUnit));
                        }
                    }
                }else{
                    self::$logger->trace(Logger::LOG_WARING,"PHPErrorLog","parse","mysql error:".self::$db->getLastError().";data:".json_encode($logUnit));
                }

            }
        }
    }

}