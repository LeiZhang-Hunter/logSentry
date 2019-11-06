<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2019/1/31
 * Time: 10:46
 */
class User extends BaseController{

    //用户列表
    public function index()
    {
        $page = (int)FactoryController::get("page",1);
        $limit = (int)FactoryController::get("limit",15);
        $list = $this->userModel->where("state",-1,">")->search([],$page,$limit);
        $count = $this->userModel->searchCount(true);
        $this->load->loadFile("PageLibrary",["Library"]);
        $pageInstance = new PageLibrary($count,$limit);
        $pageShow = $pageInstance->show();
        $this->loadView->display("Admin/User/index",[
            "list"=>$list,
            "show"=>$pageShow
        ]);
    }

    //添加用户
    public function add()
    {
        $title = "添加用户";
        $this->loadView->display("Admin/User/deal",[
            "title"=>$title
        ]);
    }


    //修改用户
    public function update()
    {
        $id = FactoryController::get("id");
        $title = "修改用户信息";
        $info = $this->userModel->where("id",$id)->getInfo();
        $this->loadView->display("Admin/User/deal",[
            "info"=>$info,
            "title"=>$title
        ]);
    }

    //添加修改接口
    public function deal()
    {
        $id = FactoryController::post("id");
        $username = FactoryController::post("username");
        $password = FactoryController::post("password");
        $true_name = FactoryController::post("true_name");

        $data = [];

        if($username == "")
        {
            $this->apiEcho(ReturnJon::ERR_PARAM,"参数错误");
        }

        $data["username"] = $username;

        if($true_name == "")
        {
            $this->apiEcho(ReturnJon::ERR_PARAM,"真实姓名不能为空");
        }
        $data["true_name"] = $true_name;

        if(!$id && $password == "")
        {
            $this->apiEcho(ReturnJon::ERR_PARAM,"密码不能为空");
        }

        if($password)
        {
            $data["password"] = md5(ssl_encrypt($password));
        }

        $data["state"] = 1;
        if(!$id)
        {
            $msg = "添加成员";
            $result = $this->userModel->add($data);
        }else{
            $msg = "修改成员";
            $result = $this->userModel->where("id",$id)->modify($data);
        }

        if($result)
        {
            $this->apiEcho(ReturnJon::ERR_OK,$msg."成功");
        }else{
            $this->apiEcho(ReturnJon::ERR_ERROR,$msg."失败");
        }
    }
}