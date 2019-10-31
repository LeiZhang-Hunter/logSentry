<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/18
 * Time: 15:55
 */

//自动加载
spl_autoload_register(function ($var){
    $dir = realpath(__ROOT__."/Vendor/".str_replace("\\","/",$var).".php");
    if(is_file($dir)) {
        include_once $dir;
    }
});