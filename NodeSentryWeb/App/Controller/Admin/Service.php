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
    public function add()
    {
        $title = "添加服务";
        $this->loadView->display("Admin/Service/make",[
            "title"=>$title
        ]);
    }

    /**
     * Description:删除和添加操作
     */
    public function deal()
    {
        $id = FactoryController::post("id");
        $clienr_ip = FactoryController::post("sentry_ip");
        if(!$id)
        {
            $message = "添加";
        }else{
            $message = "修改";
        }

        $result = $this->nodeSentryModel->checkClientIp($id,$clienr_ip);
        if($result)
        {
            $this->apiEcho(ReturnJon::ERR_ERROR,"客户机ip地址已经存在");
        }

        $this->changeData("nodeSentryModel",$message);
    }

    /**
     * Description:添加操作
     */
    public function update()
    {
        $title = "修改客户机";
        $id = FactoryController::get("id");
        $clientInfo = $this->clientModel->getClientInfoById($id);
        $this->loadView->display("Admin/Service/make",[
            "info"=>$clientInfo,
            "title"=>$title
        ]);
    }
}