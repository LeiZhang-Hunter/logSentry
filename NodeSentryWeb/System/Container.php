<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-4-16 0016
 * Time: 15:41
 */
class Container implements BaseInterface
{
    //函数注册存储数组
    protected static $functionList;

    protected static $classCollect;


    //注入容器实例
    public static function injectContainerFun($functionName,$function){
        if(!isset(self::$functionList[$functionName]))
        {
            if(!$function instanceof Closure)
            {
                productError("the register must be function");
            }
            self::$functionList[$functionName] = $function;
        }
    }

    //注入容器实例
    public static function injectContainerInstance($className,$classObject)
    {
        $className = strtolower($className);
        if(!isset(self::$classCollect[$className]) || !self::$classCollect[$className])
        {

            self::$classCollect[$className] = $classObject;
        }
    }

    //删除注册的函数
    public function deleteInjectFun($functionName)
    {
        if(isset(self::$functionList[$functionName]))
        {
            if(self::$functionList[$functionName] instanceof Closure)
            {
                unset(self::$functionList[$functionName]);
            }
        }
    }

    //删除注册的实例
    public function deleteInjectInstance($instanceName)
    {
        if(isset(self::$classCollect[$instanceName]))
        {
            unset(self::$classCollect[$instanceName]);
        }
    }

    //激活函数
    public static function active($functionName,$params = [])
    {
        // TODO: Implement active() method.
        if(!isset(self::$functionList[$functionName]))
        {//如果说不存在函数
            productError("pleace register function");
        }
        call_user_func_array(self::$functionList[$functionName],$params);
    }

    //获取注入实例
    public static function getInstance($instanceName)
    {
        //如果说存在
        if(isset(self::$classCollect[$instanceName]))
        {
            return self::$classCollect[$instanceName];
        }else{
            return NULL;
        }
    }

    //注入对象函数
    public function injectObjectMethod($object,$methodName,$method)
    {

        if(!isset($object->$methodName))
        {
//            Closure::bind($method,$object,'static');
            $object->$methodName = $method();
            return true;
        }else{
            return false;
        }
    }


}