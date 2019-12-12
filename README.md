#如何安装logSentry

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