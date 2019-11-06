<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/24
 * Time: 9:29
 */
class BaseController extends FactoryController{

    protected static $userInfo;

    /**
     * @var userModel
     */
    public $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->load->model("userModel");
        $result = userModel::checkLogin();
        if(!$result)
        {
            headerUrl(base_url("/Index/welcomeSyslog"));
        }
        self::$userInfo = $result;
    }


}