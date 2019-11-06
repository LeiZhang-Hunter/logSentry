<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-4-16 0016
 * Time: 19:15
 */
class LoaderLibrary extends Container {
    public $factory;

    //检查工厂是否存在
    private function checkFactory()
    {
        if($this->factory === NULL)
        {
            productError("factory object error");
        }
    }

    //加载model
    public function model($modelName,$param = [])
    {
        $this->checkFactory();
        if($param)
        {
            $base_dir = implode('/', $param);
        }else{
            $base_dir = "";
        }
        $include_factory_dir = __APP__."/Model/".$modelName.'.php';
        if(!isset($this->factory->$modelName)) {
            if (is_file($include_factory_dir)) {
                include_once $include_factory_dir;
                if(class_exists($modelName)) {
                    $this->factory->$modelName = new $modelName;

                }
            } else {
                productError("$modelName is not exist");
            }
        }
    }

    //加载trait 实现多继承
    public function loadFile($fileName,array $param = [])
    {
        if($param)
        {
            $base_dir = implode('/', $param);
        }else{
            $base_dir = "";
        }
        $include_factory_dir = __SYS__.'/'.$base_dir."/".$fileName.'.php';
        if(is_file($include_factory_dir))
        {
            include_once $include_factory_dir;
        }else{
            productError("$fileName is not exist");
        }
    }

    //加载trait
    public function loadTrait($fileName,array $param = [])
    {
        if($param)
        {
            $base_dir = implode('/', $param);
        }else{
            $base_dir = "";
        }
        $include_factory_dir = __SYS__."/Trait/".'/'.$base_dir."/".$fileName.'.php';
        if(is_file($include_factory_dir))
        {
            include_once $include_factory_dir;
        }else{
            productError("$fileName is not exist");
        }
    }

    //加载系统核心类库
    public function loadSysLibrary($libraryName)
    {
        $this->checkFactory();
        //如果说没有注入过
        if(!isset($this->factory->$libraryName))
        {
            $baseDir = __SYSLIB__."/".$libraryName."Library.php";
            if(is_file($baseDir))
            {
                include_once $baseDir;
                $libraryClass = $libraryName."Library";
                if(class_exists($libraryClass))
                {
                    $this->factory->$libraryName = new $libraryClass;
                }else{
                    productError("$libraryName library is not exist");
                }
            }else{
                $this->factory->$libraryName = NULL;
            }
        }
    }

    //加载第三方扩展
    public function loadVendor($venderName,array $param=[])
    {
        if($param)
        {
            $base_dir = implode('/', $param);
        }else{
            $base_dir = "";
        }
        $include_factory_dir = __VENDOR___.'/'.$base_dir."/".$venderName.'.php';
        if(is_file($include_factory_dir))
        {
            include_once $include_factory_dir;
        }else{
            productError("$venderName is not exist");
        }
    }

    //加载自己写的类库
    public function loadLibrary($libraryName,$param = [])
    {
        $this->checkFactory();
        if($param)
        {
            $base_dir = implode('/', $param);
        }else{
            $base_dir = "";
        }
        //如果说没有注入过
        if(!isset($this->factory->$libraryName))
        {
            $baseDir = __ROOT__."/Library/".'/'.$base_dir."/".$libraryName.".php";
            if(is_file($baseDir))
            {
                include_once $baseDir;
                if(class_exists($libraryName))
                {
                    $this->factory->$libraryName = new $libraryName;
                }else{
                    productError("$libraryName library is not exist");
                }
            }else{
                $this->factory->$libraryName = NULL;
            }
        }
    }

    //加载系统核心服务
    public function loadService($serviceName,$param = [])
    {
        $this->checkFactory();
        if($param)
        {
            $base_dir = implode('/', $param);
        }else{
            $base_dir = "";
        }
        //如果说没有注入过
        if(!isset($this->factory->$serviceName))
        {
            $baseDir = __SYS__."/Service/".'/'.$base_dir."/".$serviceName."_service.php";
            if(is_file($baseDir))
            {
                include_once $baseDir;
                $serviceName .= "_service.php";
                if(class_exists($serviceName))
                {
                    $this->factory->$serviceName = new $serviceName;
                }else{
                    productError("$serviceName library is not exist");
                }
            }
        }
    }

}