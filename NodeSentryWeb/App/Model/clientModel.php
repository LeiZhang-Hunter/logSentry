<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/23
 * Time: 15:17
 */

class clientModel extends Model{
    public $table = "client_ip";

    public function getList($ip = '')
    {
        if($ip)
        {
            $this->where("client_ip",$ip);
        }

        $this->where("state",-1,">");
        $this->orderBy("created_time","desc");
        $list = $this->search();
        return $list ? $list : [];
    }

    public function checkClientIp($id,$client_ip)
    {
        $result = $this->where("id",$id,"!=")->where("state",-1,">")->where("client_ip",$client_ip)->getInfo();
        return $result;
    }

    public function getClientInfoById($id)
    {
        $result = $this->where("state",-1,">")->where("id",$id)->getInfo();
        return $result;
    }


}