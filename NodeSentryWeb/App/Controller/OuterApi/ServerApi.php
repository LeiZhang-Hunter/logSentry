<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-11-4
 * Time: 下午5:55
 */
class ServerApi extends FactoryController{

    //启动server
    public function serverOpen()
    {
        //服务的id
        $server_id = $this->input->get("server_id");
    }

}