<?php
/**
 * Created by PhpStorm.
 * User: Abel
 * Date: 2018-4-2 0002
 * Time: 19:36
 */
interface BaseInterface
{
    //注册闭包函数
    public static function injectContainerFun($functionName,$function);

    //注册实例
    public static function injectContainerInstance($className,$classObject);

    //删除注册的函数
    public function deleteInjectFun($functionName);

    //删除注册的实例
    public function deleteInjectInstance($instanceName);

    //激活函数
    public static function active($functionName,$params);

    public static function getInstance($instanceName);

}