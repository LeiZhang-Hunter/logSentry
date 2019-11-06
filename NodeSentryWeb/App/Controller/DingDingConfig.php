<?php
/**
 * Description:这里是用来下发钉钉配置的
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2019/1/9
 * Time: 19:36
 */
class DingDingConfig extends FactoryController{
    public function config()
    {
        $config = config("DingDing");
        $this->apiEcho(ReturnJon::ERR_OK,$config);
    }
}