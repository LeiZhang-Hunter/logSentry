<?php
define("ENV","develop");
include_once "autoload.php";
ini_set("display_errors",true);
use \Structural\System\ConfigStruct;

$config_instance = \Pendant\SysConfig::getInstance();

//获取ip和端口
$server_list = $config_instance->getSysConfig()[ConfigStruct::SERVER];



$sys_socket = \Pendant\SwooleSysSocket::getInstance();

foreach ($server_list as $server)
{
    $sys_socket->addMonitor($server[ConfigStruct::S_IP],$server[ConfigStruct::S_PORT],$server[ConfigStruct::S_TYPE],$server[ConfigStruct::S_CONTROLLER]);
}

//注册触发前的钩子函数
$sys_socket->regBeforeHook(function () use ($config_instance){
    //注入配置文件
    $this->config = $config_instance;
    //加入常用的命令，在运行程序前加入start stop 和 reload 将进程服务化
    global $argv;

    $command = isset($argv[1]) ? $argv[1] : "";

    $server_pid_file = __ROOT__."/Proc/server.pid";
    $fp = fopen($server_pid_file,"a+");
    if(is_file($server_pid_file))
    {
        $server_pid = (int)file_get_contents($server_pid_file);
    }else{
        $server_pid = 0;
    }

    if($command == "start")
    {

        if(!flock($fp,LOCK_EX))
        {
            exit("sentry server has running\n");
        }

    }else if($command == "stop")
    {
        //发送信号让程序停止
        if($server_pid) {
            if(!posix_kill($server_pid,SIGTERM))
            {
                exit("sentry server has running\n");
            }
        }

        exit("syslog server already stop\n");

    }else if($command == "reload")
    {
        if(!$server_pid)
        {
            exit("sentry server has running\n");
        }

        posix_kill($server_pid,SIGUSR1);
        exit();
    }else{
        exit("must input start|stop|reload\n");
    }

});


//运行程序
$sys_socket->run();
