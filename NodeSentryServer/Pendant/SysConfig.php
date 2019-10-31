<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/16
 * Time: 14:46
 */
namespace Pendant;
class SysConfig{

    static $instance;


    private static $finishHook;

    private static $sysConfig;

    public static function getInstance()
    {
        self::$instance = new self();
        return self::$instance;
    }



    public static function regFinishFunction($finishFunction)
    {
        self::$finishHook = $finishFunction;
    }

    public function getSysConfig($config = "config")
    {
        if(!isset(self::$sysConfig[$config]))
        {
            self::$sysConfig[$config] = include_once CONFIG_DIR."/".$config.".php";
        }

        if(self::$finishHook && is_callable(self::$finishHook))
        {
            //执行程序
            call_user_func_array(self::$finishHook,[self::$instance]);
        }

        return self::$sysConfig[$config];
    }
}