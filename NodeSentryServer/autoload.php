<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/16
 * Time: 14:51
 */



//定义常量
define("__ROOT__",__DIR__);

define("CONFIG_DIR",realpath(__DIR__."/Config/".ENV));



//自动加载
spl_autoload_register(function ($var){
    $dir = realpath(__ROOT__."/".str_replace("\\","/",$var).".php");
    if(is_file($dir)) {
        include_once $dir;
    }
});