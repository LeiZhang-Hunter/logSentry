<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-4-2 0002
 * Time: 19:33
 */
//自动注册函数
spl_autoload_register(function($namespace){
    $dir = __DIR__."/".$namespace.'.php';
    if(is_file($dir)){
        include_once $dir;
    }
});

class SuperFactory{

    private static $instance = [];

    public function __construct(){}

    //工厂
    public function getInstance($class,array $param = [])
    {
        if(isset(self::$instance[$class]))
        {
            return self::$instance[$class];
        }else{
            if(class_exists($class))
            {//如果说存在类
                $classObject = new $class;
                self::$instance[$class] = $classObject;
            }else{//不存在
                if($param) {
                    $base_dir = implode('/', $param);
                }else{
                    $base_dir = "";
                }
                $include_factory_dir = __DIR__.'/'.$base_dir."/".$class.'.php';

                if(is_file($include_factory_dir))
                {
                    include_once $include_factory_dir;
                    if(class_exists($class))
                    {
                        $classObject = new $class;
                        self::$instance[$class] = $classObject;
                    }else{
                        productError("factory $class is not exist");
                    }
                }else{

                    productError("factory $class is not exist");
                }
            }
            return self::$instance[$class];
        }
    }


    //加载扩展
    public function loadVender($venderName,array $param = [])
    {
        if($param)
        {
            $base_dir = implode('/', $param);
        }else{
            $base_dir = "";
        }
        $include_factory_dir = __ROOT__.'/Vender/'.$base_dir."/".$venderName.'.php';
        if(is_file($include_factory_dir))
        {
            include_once $include_factory_dir;
        }else{
            productError("$venderName is not exist");
        }
    }

    //加载扩展
    public function loadCore($coreName,array $param = [])
    {
        if($param)
        {
            $base_dir = implode('/', $param);
        }else{
            $base_dir = "";
        }
        $include_factory_dir = __ROOT__.'/Core/'.$base_dir."/".$coreName.'.php';
        if(is_file($include_factory_dir))
        {
            include_once $include_factory_dir;
        }else{
            productError("$coreName is not exist");
        }
    }


}