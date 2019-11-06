<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-4-2 0002
 * Time: 20:14
 */
header("Content-type:text/html;charset=utf-8");
define('__ROOT__',__DIR__);

//引入一个php文件
include_once "Common/Function.php";

include_once "System/SuperFactory.php";

//加载常量配置文件
config("ConstVar");

//加载路由文件
$factory = new SuperFactory();
$routeInstance = $factory->getInstance('Route',['Route']);//实例化一个路由句柄

//加载模板渲染类库
$viewInstance = $factory->getInstance('FactoryViewLibrary',['Library']);//实例化一个模板句柄

//引入load库
$loaderInstance = $factory->getInstance('LoaderLibrary',['Library']);//实例化一个模板句柄

//引入input库
$inputInstance = $factory->getInstance('InputLibrary',['Library']);

//引入文件
$loaderInstance->loadFile("FactoryController");

//实例化容器
$container = new Container();

//引入注入空间
include_once __ROOT__."/Core/InjectObject.php";


//释放容器
unset($container);

//引入model
$loaderInstance->loadFile("FactoryModel");


//注入容器实例
FactoryController::injectContainerInstance('Route',$routeInstance);
FactoryController::injectContainerInstance('FactoryViewLibrary',$viewInstance);
FactoryController::injectContainerInstance('LoaderLibrary',$loaderInstance);
FactoryController::injectContainerInstance('InputLibrary',$inputInstance);

//释放实例
unset($viewInstance);
unset($loaderInstance);

//回调路由
$routeInstance->callBack();//调用路由

unset($factory);
unset($routeInstance);


