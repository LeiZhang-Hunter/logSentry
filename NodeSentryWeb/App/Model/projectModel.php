<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/28
 * Time: 10:55
 */
class projectModel extends Model{
    public $table = "project";

    public function getList()
    {
        get_instance()->load->model("clientModel");
        get_instance()->load->model("userModel");
        $list = $this->where("state",1)->search();
        if($list) {
            foreach ($list as $key => $value) {
                $clientInfo = get_instance()->clientModel->where("state",1)->where("id", $value["client_id"])->getInfo();
                $list[$key]["client_ip"] = isset($clientInfo["client_ip"]) ? $clientInfo["client_ip"] : "";

                $userInfo = get_instance()->userModel->where("id",$value["user_id"])->where("state",1)->getInfo();
                $list[$key]["true_name"] = isset($userInfo["true_name"]) ? $userInfo["true_name"] : "";
            }
        }

        return $list ? $list : [];
    }

    public function getInfoById($id)
    {
        $info = $this->where("state",1)->where("id",$id)->getInfo([]);
        if($info)
        {
            get_instance()->load->model("userModel");
            $info["created_time"] = date("Y-m-d H:i",$info["created_time"]);
            $userModel = get_instance()->userModel;
            $userInfo = $userModel->getInfoById($info["user_id"]);
            $info["username"] = isset($userInfo["username"]) ? $userInfo["username"] : "";
        }
        return $info ? $info : [];
    }
}