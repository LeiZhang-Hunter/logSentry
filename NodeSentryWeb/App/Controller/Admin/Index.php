<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-4-16 0016
 * Time: 15:26
 */
class Index extends BaseController {

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->model("syslogModel");
        $this->load->model("clientModel");
        $week_count = 0;
        $month_count = 0;
        $all_count = 0;
        $ip_list = [];
//        $week_count = $this->syslogModel->getWeekLogCount();
//        $month_count = $this->syslogModel->getMonthLogCount();
//        $all_count = $this->syslogModel->getAllCount();
//        $ip_list = $this->clientModel->getList();
//        $ip_list = $this->syslogModel->formatListLogCount($ip_list);
        $this->loadView->display("Admin/Index/index",[
            "week_count"=>$week_count,
            "month_count"=>$month_count,
            "all_count"=>$all_count,
            "ip_list"=>$ip_list
        ]);
    }
}