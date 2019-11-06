<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-4-16 0016
 * Time: 14:28
 */
class Index extends FactoryController {

    public static $backList = [
        [
            "name"=>"社区日志系统(测试)",
            "url"=>"http://syslog.oa.com/Index/welcomeSyslog"
        ],
        [
            "name"=>"oa办公系统",
            "url"=>"http://www.oa.com/"
        ],
        [
            "name"=>"车轮社区后台地址(测试)",
            "url"=>"http://community-test.chelun.com/admin.php?c=index&m=main&admin=1"
        ],
        [
            "name"=>"车轮社区后台地址(预发布)",
            "url"=>"http://chelun-pre.eclicks.cn/admin.php?c=index&m=main&admin=1"
        ],
        [
            "name"=>"车轮社区后台地址(正式)",
            "url"=>"http://community.oa.com/admin.php?c=index&m=main&admin=1"
        ],
        [
            "name"=>"接口文档地址(社区)",
            "url"=>"http://10.10.1.24:10000/index"
        ],
        [
            "name"=>"车轮后端控制中心(正式)",
            "url"=>"http://cc.oa.com/config/home/"
        ],
        [
            "name"=>"kibana(测试)",
            "url"=>"http://log1-test.oa.com/app/kibana#/management?_g=()"
        ],
        //mall-test.oa.com
        [
            "name"=>"商城后台(测试)",
            "url"=>"http://mall-test.oa.com"
        ],
        [
            "name"=>"咨询后台(测试)",
            "url"=>"http://spider-test.chelun.com"
        ],
        [
            "name"=>"app打包平台",
            "url"=>"http://code.oa.com/ "
        ],
        [
            "name"=>"小米监控",
            "url"=>"http://falcon.chelun.com/screen/9?start=-259200"
        ],
        [
            "name"=>"小程序红包程序后台(测试)",
            "url"=>"http://wish-test.oa.com"
        ],
        [
            "name"=>"模调系统",
            "url"=>"http://stats.oa.com"
        ]
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function welcome(){

        $this->loadView->display("Home/index");
    }

    public function login(){
        $this->load->model("userModel");
        $username = FactoryController::post("username");
        $password = FactoryController::post("password");
        try {
            $this->userModel->login($username, $password);
            $this->apiEcho(ReturnJon::ERR_OK,"登陆成功");
        }catch (Exception $e)
        {
            $this->apiEcho($e->getCode(),$e->getMessage());
        }
    }

    //syslog欢迎界面
    public function welcomeSyslog()
    {
        $this->load->model("userModel");

        $this->loadView->display("Admin/login");
    }

}