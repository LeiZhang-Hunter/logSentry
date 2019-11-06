<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-4-16 0016
 * Time: 18:17
 */
/**
 * 此文件用来注入依赖
 */

//引入基础类
include_once __CONTROLLER__."Admin/BaseController.php";

//引入数据库trait
$loaderInstance->loadTrait("PdoDriver");

//注入方法


LoaderLibrary::injectContainerFun('test',function($a,$b){
    var_dump($b);
    var_dump($a);die;
});
