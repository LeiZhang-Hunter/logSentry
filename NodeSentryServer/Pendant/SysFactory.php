<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/16
 * Time: 15:12
 */

namespace Pendant;

use Controller\MiddleInterface;
use ElasticSearch\Client;

use Vendor\DB;

class SysFactory
{
    private static $swoole_server;

    private static $instance;

    /**
     * handle的池子
     * @var DB
     */
    public static $protoHandle = [];

    private static $controller = [];//控制器池子

    /**
     * @var MiddleInterface
     */
    public static $interface_instance;

    public static $taskNumber;

    /**
     * @var Client
     */
    public static $es_instance;


    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    //注入处理实例
    public function regSwooleServer($proto,$protoHandle)
    {
        self::$protoHandle[$proto] = new $protoHandle;
    }

    public function setTaskNumber($taskNumber)
    {
        self::$taskNumber = $taskNumber;
    }

    public function getTaskWorkerNumber()
    {
        return self::$taskNumber;
    }

    public function regServerController($proto,$controllerName)
    {
        self::$controller[$proto] = new $controllerName($proto);
    }

    public function getServerController($proto)
    {
        return isset(self::$controller[$proto]) ? self::$controller[$proto] : NULL;
    }


}