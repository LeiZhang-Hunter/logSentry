<?php
/**
 * Description:swoole配置文件
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/16
 * Time: 14:42
 */
use \Structural\System\ConfigStruct;
return [
    ConfigStruct::SERVER=>[
        [
            ConfigStruct::S_IP=>"0.0.0.0",
            ConfigStruct::S_PORT=>"6008",
            ConfigStruct::S_TYPE=>\Structural\System\SwooleProtocol::TCP_PROTOCOL,//暂时只是支持swoole的TCP
            ConfigStruct::S_CONTROLLER=>\Controller\LogSentryController::class
        ]
    ],
    ConfigStruct::S_CPU_AFFINITY=>1,
    ConfigStruct::S_WORKER_NUM=>1,
    ConfigStruct::S_TASK_WORKER_NUM=>1,
    ConfigStruct::S_DAEMON=>false,
    //"pid_file"=>__ROOT__."/Proc/server.pid",
    ConfigStruct::S_LOG_FILE=>__ROOT__."/Log/swoole.log",
    ConfigStruct::SEN_LOG_FILE=>__ROOT__."/Log/",
    ConfigStruct::S_FILE_PRO_OBJECT=>[
        \Structural\System\SwooleProtocol::TCP_PROTOCOL=>[
            "file"=>[
                "php-fom.log"=>\Library\LogProtocol\PHPErrorLog::class
            ],
            "dir"=>[
                "/home/zhanglei/data/test2"=>\Library\LogProtocol\PHPErrorLog::class
            ],
        ]
    ]
];
