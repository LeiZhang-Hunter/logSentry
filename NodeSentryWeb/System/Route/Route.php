<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-4-2 0002
 * Time: 19:34
 */
class Route extends Container
{
    private static $module;

    private static $class;

    private static $method;

    private static $params;

    public function __construct()
    {

    }

    public function getModule()
    {
        return self::$module;
    }

    public function getController()
    {
        return self::$class;
    }

    public function getMethod()
    {
        return self::$method;
    }

    public function callBack()
    {
        $path = $_SERVER['REQUEST_URI'];

        //获取网址路由
        $urlRoute = explode('?',$path);

        $urlRoute = isset($urlRoute[0]) ? $urlRoute[0] : [];

        $urlRoute = explode('/', trim($urlRoute, '/'));
        try {
            //检出路由
            $this->checkRoute($urlRoute);
        }catch (Exception $exception)
        {
            echo $exception->getTraceAsString();
            exit(0);
        }

        $classObject = new self::$class;

        //如果说不是工厂控制器的实例
        if(!$classObject instanceof FactoryController)
        {
            productError("the base instance must be FactoryController");
        }

        $classReflection = new ReflectionClass($classObject);

        //回调检查方法
        $classReflection->getMethod(self::$method);

        //回调方法
        call_user_func_array([$classObject,self::$method],self::$params);
    }

    //回检路由
    private function checkRoute($urlRoute)
    {
        $queueOne = array_shift($urlRoute);

        $routeConfig = config('Router');

        $routeConfigModule = $routeConfig["modules"];

        //如果说没有设置模块
        $base_dir = __CONTROLLER__;
        if ($routeConfigModule) {
            //如果说存在与module中
            if (in_array($queueOne, $routeConfigModule)) {
                self::$module = $queueOne;
                $base_dir = __CONTROLLER__ . $queueOne . "/";
                $queueOne = array_shift($urlRoute);
            }
        }
        $queueOne = ($queueOne === "") ? "Index" : $queueOne;
        $loading = $base_dir . $queueOne;

        if (!is_file($loading . ".php")) {
            if (is_dir($loading)) {//如果说是文件夹
                $class = array_shift($urlRoute);
                //加载php文件类
                $loadClassFile = $loading . "/" . $class . ".php";
                if (is_file($loadClassFile)) {
                    include_once $loadClassFile;
                    if (class_exists($class)) {
                        $this->initRoutePrototype($class, $urlRoute);
                        return;
                    }
                }

            }
            productError("this controller is not exit");
        } else {
            include_once $loading . ".php";
            if (class_exists($queueOne)) {
                $this->initRoutePrototype($queueOne, $urlRoute);
            }
        }
    }

    //制作控制器
    private function initRoutePrototype($class, $urlRoute)
    {
        self::$class = $class;
        self::$method = array_shift($urlRoute);
        if (empty(self::$method)) {
            self::$method = "welcome";//默认路由
        }
        self::$params = $urlRoute;
    }
}
