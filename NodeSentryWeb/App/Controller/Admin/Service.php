<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/21
 * Time: 18:24
 */
class Service extends BaseController {

    public function __construct()
    {
        parent::__construct();
        $this->load->model("nodeSentryModel");
    }

    /**
     * Description:列表页
     */
    public function index()
    {
        $ip = FactoryController::get("ip");
        $this->nodeSentryModel->where(nodeSentryModel::F_type,1);
        $list = $this->nodeSentryModel->getList($ip);
        $this->loadView->display("Admin/Service/index",[
            "list"=>$list
            ]);
    }

    /**
     * Description:执行删除操作
     */
    public function delete()
    {
        $this->changeState("clientModel","删除");
    }

    /**
     * 改变状态操作
     */
    public function changeServiceState()
    {
        $this->changeState("clientModel","操作");
    }

    /**
     * Description:添加操作
     */
    public function addServer()
    {
        $title = "添加服务哨兵";
        $this->loadView->display("Admin/Service/make",[
            "title"=>$title
        ]);
    }

    /**
     * Description:删除和添加操作
     */
    public function dealServer()
    {
        $id = FactoryController::post("id");
        $clienr_ip = FactoryController::post("sentry_ip");
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

        $this->changeData("nodeSentryModel",$message,[
            "sentry_token"=>$this->nodeSentryModel->makeToken(),
            "config"=>json_encode($config),
            "type"=>nodeSentryModel::F_server
        ]);
    }

    /**
     * Description:添加操作
     */
    public function update()
    {
        $title = "修改服务哨兵";
        $id = FactoryController::get("id");
        $clientInfo = $this->nodeSentryModel->getSentryInfoBySentryId($id);
        $this->loadView->display("Admin/Service/make",[
            "info"=>$clientInfo,
            "title"=>$title
        ]);
    }
}