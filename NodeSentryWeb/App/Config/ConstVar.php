<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-4-2 0002
 * Time: 20:36
 */
//根目录

//系统目录
define('__SYS__',__ROOT__."/System");

//APP目录
define('__APP__',__ROOT__."/App");

//拓展目录
define('__VENDOR___',__ROOT__."/Vendor");

define('__SYSLIB__',__ROOT__."/System/Library");

//控制器目录
define('__CONTROLLER__',__ROOT__.'/App/Controller/');

//视图目录
define('__VIEW__',__ROOT__.'/App/View/');

define("__PUBLIC__","/Public/");






class ReturnJon{

    //无错误
    const ERR_OK = 0;

    //参数错误
    const ERR_PARAM = 1;

    //登陆错误
    const ERR_LOGIN = 2;

    //修改错误
    const ERR_MODIFY = 3;

    //笼统错误
    const ERR_ERROR = 4;
}