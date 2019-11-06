<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/20
 * Time: 18:43
 */

class LogList extends BaseController {



    public function __construct()
    {
        parent::__construct();
        $this->load->model('syslogModel');
    }

    /**
     * Description:日志列表接口
     */
    public function logCollect()
    {
        $page = FactoryController::get("page",1);
        $limit = FactoryController::get("limit",100);
//        $type = FactoryController::get("type");
        $project_id = (int)FactoryController::get("project_id");
        $type = 1;
        $begin_time = FactoryController::get("begin_time");
        $end_time = FactoryController::get("end_time");
        $server_ip = trim(FactoryController::get("ip"));
        $body = trim(FactoryController::get("body"));
        $php_error = trim(FactoryController::get("php_error"));
        $syslogModel = $this->syslogModel;

        $condition = [];

        //检索类别
        if($type !== "")
        {
            $condition["query"]["bool"]["must"][] = [
                "match"=>[
                    "type"=>$type
                ]
            ];

        }

        if($project_id)
        {
            $condition["query"]["bool"]["must"][] = [
                "match"=>[
                    "project_id"=>$project_id
                ]
            ];
        }

        //检索服务器ip
        if($server_ip)
        {
            $condition["query"]["bool"]["must"][] = [
                "match"=>[
                    "server_ip"=>$server_ip
                ]
            ];
        }

        if($php_error !== "")
        {
            $condition["query"]["bool"]["must"][] = [
                "match"=>[
                    "php_error_level"=>$php_error
                ]
            ];
        }

        //检索body
        if($body !== "")
        {
            $condition["query"]["bool"]["must"][] = [
                "wildcard"=>[
                    "body"=>"*".addslashes($body)."*"
                ]
            ];
        }

        if($start = strtotime($begin_time))
        {
            $condition["query"]["bool"]["must"][] = [
                "range"=>[
                    "happen_time"=>[
                        "gte"=>$start
                    ]
                ]
            ];
        }

        if($end = strtotime($end_time))
        {
            $condition["query"]["bool"]["must"][] = [
                "range"=>[
                    "happen_time"=>[
                        "lt"=>$end+24*60*60
                    ]
                ]
            ];
        }

        //倒序
        $condition["sort"]["sys_id"]["order"] = "desc";

        $condition["from"] = ($page-1)*$limit;
        $condition["size"] = $limit;
        $list = $syslogModel->searchByEs($condition);
        $count = $syslogModel->getCountByEs();
        $list=syslogModel::formatData($list);
        $this->load->loadFile("PageLibrary",["Library"]);
        $pageObject = new PageLibrary($count,$limit);
        $page_show = $pageObject->show();
        $this->loadView->display("Admin/Log/index",['list'=>$list,"show"=>$page_show]);
    }

    public function sysInfo()
    {
        $sysId = FactoryController::get("id");
        $info = $this->syslogModel->where("id",$sysId)->getInfo();
        $formatInfo = [];
        if($info)
        {
            $formatInfo = syslogModel::formatData([$info]);
        }
        $sysInfo = isset($formatInfo[0]) ? $formatInfo[0] : [];
        $this->loadView->display("Admin/Log/detail",["info"=>$sysInfo]);
    }
}