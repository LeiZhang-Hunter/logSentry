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
server: {
ip: "127.0.0.1",
port: "6008"
},
log_file: {
file_path: "/home/zhanglei/ourc/logSentry/NodeSentryClient/log/log.log"
},
file_sentry_thread_number: "1",
dir_sentry_thread_number: "1",
sentry_log_file: {
php-fom.log: "/data/logs/php/fpm-php-www.log"
},
sentry_log_dir: {
nginx/community-test.chelun.com: "/data/logs/nginx/community-test.chelun.com",
topic-service: "/data/logs/service/topic-service",
bypass-service: "/data/logs/service/bypass-service",
chelun-service: "/data/logs/service/chelun-service",
forum-service: "/data/logs/service/forum-service",
mall-service: "/data/logs/service/mall-service",
bypass-node: "/data/logs/service/bypass-service",
user-service: "/data/logs/service/user-service",
message-service: "/data/logs/service/message-service"
},
pid_file: "sentry.pid",
system: {
max_fd: "1024"
}
}
```

