<?php
/**
 * Description:项目中心
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/28
 * Time: 10:10
 */
class Project extends BaseController{

    /**
     * @var serverModel
     */
    public $projectModel;

    /**
     * @var clientModel
     */
    public $clientModel;

    public function __construct()
    {
        parent::__construct();
        $this->load->model("projectModel");
        $this->load->model("clientModel");
    }

    /**
     * Description:项目组列表
     */
    public function index()
    {
        $list = $this->projectModel->getList();
        $this->loadView->display("Admin/Project/index",[
            "list"=>$list
        ]);
    }

    /**
     * Description:添加
     */
    public function add()
    {
        $id = (int)FactoryController::get("id",0);
        if(!$id)
        {
            $title = "添加项目";
        }else{
            $title = "修改项目";
        }


        $client_list = $this->clientModel->getList();
        $info = $this->projectModel->getInfoById($id);
        $this->loadView->display("Admin/Project/make",[
            "info"=>$info,
            "title"=>$title,
            "client_list"=>$client_list
        ]);
    }

    /**
     * Description:修改
     */
    public function deal()
    {
        $username = FactoryController::post("username");
        $client_id = FactoryController::post("client_id");
        $project_name = FactoryController::post("project_name");
        $project_dir = FactoryController::post("project_dir");
        $id = FactoryController::post("id");

        $this->load->model("userModel");
        $userInfo = $this->userModel->getInfoByUsername($username);
        if(!$userInfo)
        {
            $this->apiEcho(ReturnJon::ERR_PARAM,"用户账号不存在");
        }

        if($client_id === "")
        {
            $this->apiEcho(ReturnJon::ERR_PARAM,"请选择客户机ip地址");
        }

        if($project_name === "")
        {
            $this->apiEcho(ReturnJon::ERR_PARAM,"请输入项目名称");
        }

        if($project_dir === "")
        {
            $this->apiEcho(ReturnJon::ERR_PARAM,"请输入项目路径");
        }
        $msg = "";
        if($id)
        {
            $msg = "添加项目";
            $result = $this->projectModel->where("id",$id)->modify([
                "user_id"=>$userInfo["id"],
                "client_id"=>$client_id,
                "project_name"=>$project_name,
                "project_dir"=>$project_dir,
            ]);
        }else{
            $msg = "修改项目";
            $result = $this->projectModel->add([
                "user_id"=>$userInfo["id"],
                "client_id"=>$client_id,
                "project_name"=>$project_name,
                "project_dir"=>$project_dir,
            ]);
        }
        if($result)
        {
            $this->apiEcho(ReturnJon::ERR_OK,$msg."成功");
        }else{
            $this->apiEcho(ReturnJon::ERR_ERROR,$msg."失败");
        }
    }


}