<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-4-16 0016
 * Time: 16:06
 */
class FactoryController extends Container {

    private static $instance;

    public $input;

    public $load;

    /**
     * @var FactoryViewLibrary
     */
    public $loadView;

    public $route;

    public function __construct()
    {
        if(!self::$instance) {
            self::$instance =& $this;
        }
        //加载loader库
        $this->load = self::$classCollect['loaderlibrary'];
        $this->load->factory = self::$instance;
        //释放loader
        unset(self::$classCollect['loaderlibrary']);

        //加载视图库
        $this->loadView = self::$classCollect['factoryviewlibrary'];
        //释放视图库
        unset(self::$classCollect['factoryviewlibrary']);

        //加载路由情况的ku
        $this->route = self::$classCollect['route'];
        //释放路由库
        unset(self::$classCollect['route']);

        $this->input = self::$classCollect['inputlibrary'];
        //释放input库
        unset(self::$classCollect['inputlibrary']);
    }

    public static function &get_instance()
    {
        return self::$instance;
    }

    public static function get($key,$defaultValue='')
    {
        return (self::$instance->input->get($key) !== null) ? self::$instance->input->get($key) : $defaultValue;
    }

    public static function post($key,$defaultValue='')
    {
        return self::$instance->input->post($key) !== null ? self::$instance->input->post($key) : $defaultValue;
    }

    public static function cookie($key,$defaultValue='')
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : "";
    }

    /**
     * Description:输出删除
     * @param $code
     * @param $message
     */
    public function apiEcho($code,$message)
    {
        ob_clean();
        exit(json_encode(["code"=>$code,"response"=>$message]));
    }

    public function changeState($model,$msg)
    {
        $this->load->model($model);
        $delete_id = (int)FactoryController::post("delete_id");
        $state = (int)FactoryController::post("state");
        if($delete_id <= 0)
        {
            $this->apiEcho(ReturnJon::ERR_PARAM,"删除主键不能是小于等于0的数字");
        }

        //执行删除操作
        $result = $this->clientModel->where("id",$delete_id)->modify(["state"=>$state]);
        if($result)
        {
            $this->apiEcho(ReturnJon::ERR_OK,$msg."成功");
        }else{
            $this->apiEcho(ReturnJon::ERR_MODIFY,$msg."失败");
        }
    }

    public function changeData($model,$message)
    {
        $data = $this->input->post();
        if(!$data)
        {
            $this->apiEcho(ReturnJon::ERR_PARAM,$message."失败");
        }

        foreach ($data as $key=>$value)
        {
            if($value === "")
            {
                $this->apiEcho(ReturnJon::ERR_PARAM,$message."失败");
            }else{
                $data[$key] = trim($value);
            }
        }

        $id = isset($data["id"]) ? $data["id"] : 0;

        if(isset($data["id"]))
        {
            unset($data["id"]);
        }

        $this->load->model($model);

        if($id) {
            $result = $this->$model->where('id',$id)->modify($data);
        }else{
            $result = $this->$model->add($data);
        }

        if($result)
        {
            $this->apiEcho(ReturnJon::ERR_OK,$message."成功");
        }else{
            $this->apiEcho(ReturnJon::ERR_ERROR,$message."失败");
        }
    }

}