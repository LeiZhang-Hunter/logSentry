#如何安装logSentry

联系人:张磊

联系微信：zl357733652

联系qq:357733652

qq邮箱:357733652@qq.com

####cmake 安装 NodeSentryClient

    mkdir build

    cd build

    cmake ..

    make -j4


这样我们就可以顺利生成logSentry的二进制文件

然后使用命令

./logSentry -c  配置文件路径  就可以使用了

logSentry 采用异步多线程的模型

配置文件详解:

[network_url]->url  这个配置选项是从云端获取等价的json配置

json格式:

```
{
	"server": {
		"ip": "127.0.0.1",
		"port": "6008"
	},
	"log_file": {
		"file_path": "/home/zhanglei/ourc/logSentry/NodeSentryClient/log/log.log"
	},
	"file_sentry_thread_number": "1",
	"dir_sentry_thread_number": "1",
	"sentry_log_file": {
		"php-fom.log": "/data/logs/php/fpm-php-www.log"
	},
	"sentry_log_dir": {
		"nginx/community-test.chelun.com": "/data/logs/nginx/community-test.chelun.com",
		"topic-service": "/data/logs/service/topic-service",
		"bypass-service": "/data/logs/service/bypass-service",
		"chelun-service": "/data/logs/service/chelun-service",
		"forum-service": "/data/logs/service/forum-service",
		"mall-service": "/data/logs/service/mall-service",
		"bypass-node": "/data/logs/service/bypass-service",
		"user-service": "/data/logs/service/user-service",
		"message-service": "/data/logs/service/message-service"
	},
	"pid_file": "sentry.pid",
	"system": {
		"max_fd": "1024"
	}
}
```

其中server是服务端的端口 port是服务端的ip

log_file 是logSentry的日志存放地点

file_sentry_thread_number是监控文件需要打开的线程数目

dir_sentry_thread_number 这个是监控目录所需要的工作线程数目

sentry_log_file 是监控的文件的集合

sentry_log_dir 是监控的目录的集合

pid_file 是哨兵的pid文件位置

system是系统参数 max_fd是足底啊打开的描述符数目，现在没有用没有做处理

####哨兵服务器

由于本人时间匆忙 哨兵服务器采用php编写并没有采用c++

配置文件详解：

Config 下面的 develop文件夹下是配置文件位置

config.php

```
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
            ConfigStruct::S_IP=>"0.0.0.0",//监听ip
            ConfigStruct::S_PORT=>"6008",//监听端口
            ConfigStruct::S_TYPE=>\Structural\System\SwooleProtocol::TCP_PROTOCOL,//暂时只是支持swoole的TCP
            ConfigStruct::S_CONTROLLER=>\Controller\LogSentryController::class//当收到数据的时候先触发的控制器
        ]
    ],
    ConfigStruct::S_CPU_AFFINITY=>1,//是否开启cpu亲和模式
    ConfigStruct::S_WORKER_NUM=>1,//工作进程数目
    ConfigStruct::S_TASK_WORKER_NUM=>1,//任务进程数目
    ConfigStruct::S_DAEMON=>false,//是否是守护进程
    //"pid_file"=>__ROOT__."/Proc/server.pid",//pid位置
    ConfigStruct::S_LOG_FILE=>__ROOT__."/Log/swoole.log",//swoole日志位置
    ConfigStruct::SEN_LOG_FILE=>__ROOT__."/Log/",//logSentryServer 存放的日志位置
    ConfigStruct::S_FILE_PRO_OBJECT=>[
        \Structural\System\SwooleProtocol::TCP_PROTOCOL=>[
            "file"=>[//文件监控，当对应的文件数据上报时候触发的类
                "php-fom.log"=>[
                    "handle"=>\Library\LogProtocol\PHPErrorLog::class,
                    "split"=>PHP_EOL
                ]
            ],
            "dir"=>[//目录监控，当对应的文件数据上报时候触发的类
                "nginx/community-test.chelun.com"=>[
                    "handle"=>\Library\LogProtocol\SplitLog::class,
                    "split"=>PHP_EOL
                ],
                "topic-service"=>[
                    "handle"=>\Library\LogProtocol\SplitLog::class,
                    "split"=>PHP_EOL
                ],
                "bypass-service"=>[
                    "handle"=>\Library\LogProtocol\SplitLog::class,
                    "split"=>PHP_EOL
                ],
                "chelun-service"=>[
                    "handle"=>\Library\LogProtocol\SplitLog::class,
                    "split"=>PHP_EOL
                ],
                "forum-service"=>[
                    "handle"=>\Library\LogProtocol\SplitLog::class,
                    "split"=>PHP_EOL
                ],
                "mall-service"=>[
                    "handle"=>\Library\LogProtocol\SplitLog::class,
                    "split"=>PHP_EOL
                ],
                "bypass-node"=>[
                    "handle"=>\Library\LogProtocol\SplitLog::class,
                    "split"=>PHP_EOL
                ],
                "user-service"=>[
                    "handle"=>\Library\LogProtocol\SplitLog::class,
                    "split"=>PHP_EOL
                ],
                "message-service"=>[
                    "handle"=>\Library\LogProtocol\SplitLog::class,
                    "split"=>PHP_EOL
                ],
            ],
        ]
    ]
];

```

db.php

```
<?php
/**
 * Description:mysql数据库配置文件
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/17
 * Time: 17:16
 */
return [
    "database_name"=>"syslog",
    "ip"=>"127.0.0.1",
    "port"=>"3306",
    "charset"=>"utf8",
    "username"=>"root",
    "password"=>"root"
];
```

es.php Es的配置

```
<?php
/**
 * Description:ES配置文件
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/18
 * Time: 12:36
 */
return [
    "ip"=>"127.0.0.1",
    "port"=>"9200",
    "index"=>"syslog",
    "type"=>"syslog",
];
```