<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2019/1/8
 * Time: 19:23
 */
class DingDing extends BaseController {
    public function index()
    {
        $config = config("DingDing");
        $this->loadView->display("Admin/DingDing/config",[
            "config"=>$config
        ]);
    }

    public function set()
    {
        $config = FactoryController::post("config");
        $dir = __APP__."/Config/DingDing.php";
        if(!is_file($dir))
        {
            $this->apiEcho(ReturnJon::ERR_ERROR,"配置文件不存在");
        }
        $text="<?php \n return ".var_export($config,true).';';
        $result = @file_put_contents($dir, $text);
        if($result)
        {
            $this->apiEcho(ReturnJon::ERR_OK,"设置成功");
        }else{
            $this->apiEcho(ReturnJon::ERR_ERROR,"设置失败");
        }
    }
}