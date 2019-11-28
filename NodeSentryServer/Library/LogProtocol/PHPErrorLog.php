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

class PHPErrorLog implements ResolveProtocol {
    //这是php所有的错误等级
    static $php_error = [
        "Fatal error",
        "Recoverable fatal error",
        "Warning",
        "Parse error",
        "Notice",
        "Strict Standards",
        "Deprecated",
        "Unknown error"
    ];

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
    public static function parse($bufferInfo,$sentry_type)
    {

        $buffer = $bufferInfo[LogSentryStruct::Buf_body];
        $data = array_filter(explode("\n",$buffer));

        if(!$data)
        {
            self::$logger->trace(Logger::LOG_WARING,self::class,"onReceive","buffer type [$sentry_type] error");
            return false;
        }

        if(!$sentry_type)
        {
            $monitor_file = $bufferInfo[LogSentryStruct::File_name];
        }else{
            $monitor_file = $bufferInfo[LogSentryStruct::Dir_name];
        }

        if(!$monitor_file)
        {
            self::$logger->trace(Logger::LOG_WARING,self::class,"onReceive","log file [$monitor_file] error");
            return false;
        }


        //循环内容
        foreach ($data as $dataUnit)
        {
            if($dataUnit) {

                //分割出日志的主体内容
                $position = strpos($dataUnit,"]");

                if(!$position)
                {
                    self::$logger->trace(Logger::LOG_WARING,
                        "PHPErrorLog","parse","get time end ] error;content :".$dataUnit);
                    continue;
                }

                //主体内容计算出来
                $main_body = substr($dataUnit,$position+1);
                if(!$main_body)
                {
                    self::$logger->trace(Logger::LOG_WARING,
                        "PHPErrorLog","parse","get main body  error;content :".$dataUnit);
                    continue;
                }

                //计算出主体的token内容
                $main_token = md5($main_body);

                //去数据库查询是否有这个token
                try {
                    $info = self::$db->where("body_token", $main_token)->find("sys_syslog");
                    if($info === false)
                    {
                        self::$logger->trace(Logger::LOG_WARING,
                            "PHPErrorLog","parse","get body info error;".self::$db->getLastError());
                        continue;
                    }
                }catch (\Exception $exception)
                {
                    $info = false;
                    continue;
                }

                //查不到数据就执行插入
                if($info === []){
                    /**
                     * 过滤php错误级别
                     */
                    foreach (PHPErrorLog::$php_error as $error_key=>$error_value)
                    {

                        if(strpos($dataUnit,($error_value)) !== false)
                        {
                            $logUnit = [];
                            $logUnit[LogDbStruct::Sentry_type] = $sentry_type;
                            $logUnit[LogDbStruct::Sentry_file] = md5($monitor_file);//md5编码方便查询
                            $logUnit[LogDbStruct::Client_ip] = $bufferInfo[LogDbStruct::Client_ip];
                            $logUnit[LogDbStruct::Happen_time] = $bufferInfo[LogSentryStruct::Time];
                            $logUnit[LogDbStruct::Body] = htmlspecialchars(addslashes($dataUnit));//内容
                            $logUnit[LogDbStruct::Php_error_level] = $error_key;//php报错级别
                            $logUnit[LogDbStruct::Body_token] = md5($main_body);
                            $logUnit[LogDbStruct::Created_time] = time();
                            $logUnit[LogDbStruct::State] = 1;//正常状态
                            $logUnit[LogDbStruct::Type] = 1;//级别为php日志

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
                            break;
                        }
                    }
                }else if($info){
                    //查到了数据进行更新
                    self::$db->where("body_token", $main_token)->update("sys_syslog",[
                        LogDbStruct::Happen_time=>time(),
                        LogDbStruct::Deal_state=>1
                    ]);

                    //更新es库
                    $info[LogDbStruct::Happen_time] = time();
                    $info[LogDbStruct::Deal_state] = 1;
                    self::$es->client->index($info,$info[LogDbStruct::Id]);
                }

            }
        }
    }

}