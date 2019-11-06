<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-4-16 0016
 * Time: 16:41
 */
class FactoryViewLibrary extends Container
{
    //渲染模板
    public function display($loadDir,array $data = [])
    {
        extract($data);
        $loadViewDir = __VIEW__.$loadDir.".php";
        if(is_file($loadViewDir))
        {
            include_once $loadViewDir;
        }else{
            productError("this view $loadViewDir is not exist");
        }
    }
}