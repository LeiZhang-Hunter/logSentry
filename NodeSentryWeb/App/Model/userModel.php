<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/21
 * Time: 17:08
 */
//用户model

class userModel extends Model{

    public $table = "user";

    //登陆
    public function login($username,$password)
    {
        if($username === "")
        {
            throw new Exception("请输入用户名",ReturnJon::ERR_PARAM);
        }

        if($password === "")
        {
            throw new Exception("请输入密码",ReturnJon::ERR_PARAM);
        }
        $this->where("username",$username);
        $encrypt_password = md5(ssl_encrypt($password));
        $this->where("password",$encrypt_password);
        $this->where("state",1);
        $info = $this->getInfo();
        if($info)
        {
            setcookie("user_token",ssl_encrypt(json_encode($info)),time()+3600*24*7,"/");
            return $info;
        }else{
            throw new Exception("用户名或密码错误",ReturnJon::ERR_LOGIN);
        }
    }

    public static function checkLogin()
    {
        $user_token = FactoryController::cookie("user_token");
        if(!$user_token)
        {
            ob_clean();
            headerUrl(base_url("/"));
            exit();
        }
        $result = self::decryptCookie($user_token);
        return $result;
    }

    public static function decryptCookie($str)
    {
        $str = ssl_decrypt($str);
        return json_decode($str,1);
    }

    public function getInfoByUsername($username)
    {
        $info = $this->where("username",$username)->where("state",1)->getInfo();
        return $info;
    }

    public function getInfoById($id)
    {
        $info = $this->where("id",$id)->where("state",1)->getInfo();
        return $info;
    }
}