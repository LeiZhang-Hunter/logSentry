<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-10-31
 * Time: 下午7:48
 */
namespace Structural\System;
class ConfigStruct{
    const SERVER = "server";
    const S_IP = "ip";
    const S_PORT = "port";
    const S_TYPE = "type";
    const S_CONTROLLER = "controller";
    const S_CPU_AFFINITY = "open_cpu_affinity";
    const S_WORKER_NUM = "worker_num";
    const S_TASK_WORKER_NUM = "task_worker_num";
    const S_DAEMON = "daemonize";
    const S_LOG_FILE = "log_file";
    const SEN_LOG_FILE = "sentry_log_file";
    const S_FILE_PRO_OBJECT = "file_protocol_object";
}