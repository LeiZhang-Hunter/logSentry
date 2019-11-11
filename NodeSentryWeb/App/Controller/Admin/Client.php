<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/21
 * Time: 18:24
 */
class Client extends BaseController {

    public function __construct()
    {
        parent::__construct();
        $this->load->model("nodeSentryModel");
    }

    //获取客户端列表
    public function getList()
    {
        $server_id = $this->input->get("server_id");
        $list = $this->nodeSentryModel->getListByServerId($server_id);
        $this->loadView->display("Admin/Client/client",[
            "list"=>$list
        ]);
    }

    /**
     * Description:添加操作
     */
    public function addClient()
    {
        $title = "添加哨兵节点";
        $server_id = $this->input->get("server_id");
        $this->loadView->display("Admin/Client/make",[
            "title"=>$title,
            "server_id"=>$server_id
        ]);
    }

    /**
     * Description:更新操作
     */
    public function updateClient()
    {
        $title = "修改哨兵节点";
        $id = FactoryController::get("id");
        $server_id = $this->input->get("server_id");
        $clientInfo = $this->nodeSentryModel->getSentryInfoBySentryId($id);
        $this->loadView->display("Admin/Client/make",[
            "info"=>$clientInfo,
            "title"=>$title,
            "server_id"=>$server_id
        ]);
    }

    /**
     * Description:删除和添加操作
     */
    public function dealClient()
    {
        $id = FactoryController::post("id");
        if(!$id)
        {
            $message = "添加";
        }else{
            $message = "修改";
        }

        $name = $this->input->post("name");
        if(!$name)
        {
            $this->apiEcho(ReturnJon::ERR_ERROR,"哨兵名字不能为空");
        }

        $config = $this->input->post("config");
        if(!$config)
        {
            $this->apiEcho(ReturnJon::ERR_ERROR,"哨兵配置不能为空");
        }

        $server_id = $this->input->get("server_id");
        if(!$server_id)
        {
            $this->apiEcho(ReturnJon::ERR_ERROR,"服务器哨兵不能为空");
        }

        $this->changeData("nodeSentryModel",$message,[
            "server_id"=>$server_id,
            "sentry_token"=>$this->nodeSentryModel->makeToken(),
            "config"=>json_encode($config),
            "type"=>nodeSentryModel::F_client
        ]);
    }
}