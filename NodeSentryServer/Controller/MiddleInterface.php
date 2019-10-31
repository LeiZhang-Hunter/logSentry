<?php
/**
 * Description:数据中间层
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/17
 * Time: 15:50
 */
namespace Controller;

use Pendant\SysFactory;
use Pendant\TcpProtocol;

class MiddleInterface{

    const SEARCH_PHP = "php";

    const SEARCH_MYSQL = "mysql";

    const SEARCH_NGINX = "nginx";

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

    static $client_ip_list = [];

    static $project_list = [];

    public function run($server)
    {
        //查出服务器中挂载的配置项，防止未验证的ip地址进入
        if(!self::$client_ip_list)
        {
            $ip_list = SysFactory::$db->where("state",1)->select("sys_client_ip");
            foreach ($ip_list as $key=>$value)
            {
                self::$client_ip_list[] = $value["client_ip"];
            }
        }

        //屏蔽没有认证的服务器
        if(!in_array(TcpProtocol::$data["client_info"]["address"],self::$client_ip_list))
        {
            return false;
        }

        //获取到所有人的项目列表
        if(!self::$project_list)
        {
            //项目列表
            $project_list = SysFactory::$db->where("state",1)->select("sys_project");
            $data = [];
            foreach ($project_list as $key=>$value)
            {
                $userInfo = SysFactory::$db->where("state",1)->where("id",$value["user_id"])->find("sys_user");
                if(!$userInfo)
                    continue;

                $data[$value["id"]]["project_dir"] = $value["project_dir"];
                $data[$value["id"]]["username"] = $userInfo["username"];
                $data[$value["id"]]["user_id"] = $value["user_id"];
                $data[$value["id"]]["project_name"] = $value["project_name"];
            }
            self::$project_list = $data;
        }

        //系统日志数据包
        $data = [];
        $data["type"] = "";
        $data["facility"] = TcpProtocol::$data["facility"];
        $data["server_ip"] = TcpProtocol::$data["client_info"]["address"];
        $data["level"] = TcpProtocol::$data["severity"];
        $data["hostname"] = TcpProtocol::$data["hostname"];
        $data["happen_time"] = TcpProtocol::$data["happen_time"];
        $data["body"] = addslashes(TcpProtocol::$data["body"]);//加入转义
        $data["created_time"] = time();

        //确定是php 还是mysql 还是nginx 的消息
        $search_body = strtolower($data["body"]);

        $type = 0;
        $php_error_level = -1;

        //确认项目id
        $project_id = 0;
        foreach (self::$project_list as $key=>$value)
        {
            if(strpos($search_body,$value["project_dir"]) !== false)
            {
                $project_id = $key;
                break;
            }
        }
        $data["project_id"] = $project_id;

        if(strpos($search_body,self::SEARCH_PHP) !== false)
        {
            /**
             * 过滤php错误级别
             */
            $type = 1;
            foreach (self::$php_error as $error_key=>$error_value)
            {
                if(strpos($search_body,strtolower($error_value)) !== false)
                {
                    if($error_key == 0)
                    {
                        $task_task = [];
                        $task_task["msg"] = addslashes($search_body);
                        $task_task["project_name"] = isset(self::$project_list[$project_id]["project_name"]) ? self::$project_list[$project_id]["project_name"] : "无归属的消息";
                        $task_task["happen_time"] = date("Y-m-d H:i",TcpProtocol::$data["happen_time"]);
                        $task_task = json_encode($task_task);
                        $server->task($task_task);
                    }
                    $php_error_level = $error_key;
                    $data["php_error_level"] = $php_error_level;
                    break;
                }
            }
        }elseif (strpos($search_body,self::SEARCH_MYSQL) !== false)
        {
//            $type = 2;
            return false;
        }elseif(strpos($search_body,self::SEARCH_NGINX) !== false)
        {
//            $type = 3;
            return false;
        }

        if($php_error_level < 0)
        {
            return false;
        }
        $data["type"] = $type;



        //落地mysql
        if(SysFactory::$db->insert("sys_syslog",$data))
        {
            //获取最后插入的id 然后放入es 中这里是原子操作不需要担心安全问题
            $insertId = SysFactory::$db->getLastInsertId();
            $data["sys_id"] = (int)$insertId;
            //存入es
            if(!SysFactory::$es_instance->index($data,$insertId))
            {
                $this->writeLog($data);
            }
        }else{
            //如果出现意外日志写入文件存储防止丢失
            $this->writeLog($data);
        }
    }

    //备用方案数据库写不进去的话我们将他放到日志里，事后可以观看恢复关键日志
    private function writeLog($msg)
    {
        $data = json_encode($msg)."\n";//压缩数据
        file_put_contents(__ROOT__."/Log/".date("Y-m-d").".bak.log",$data,FILE_APPEND);
    }
}
