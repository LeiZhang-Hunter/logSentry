<?php
/**
 * Description:swoole配置文件
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/16
 * Time: 14:42
 */

return [
    "sys_ip"=>"0.0.0.0",
    "sys_port"=>"6008",
    "open_cpu_affinity"=>1,
    "open_eof_split"=>false,
    "package_eof"=>"\r\n\r\n",
    "worker_num"=>4,
    "task_worker_num"=>1,
    "daemonize"=>false,
    //"pid_file"=>__ROOT__."/Proc/server.pid",
    "log_file"=>__ROOT__."/Log/swoole.log"
];
