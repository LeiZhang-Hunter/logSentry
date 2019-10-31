<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/19
 * Time: 12:22
 */

namespace Controller;

class DingDingInterFace{

    private static $dingdingUrl = "http://syslog.oa.com/DingDingConfig/config";

    private static $config =  [
        'user' => 'yinchuanwen',
        'url' => 'http://192.168.1.159:8080/dingding/alert',
        'order_user' => 'zhoujie',
        'move_car_user' => 'zhoujie,lushangwei,chenlijun,rendong',
        'admire_shell_user' => 'yinchuanwen,wuyongjian,zhanglei1',
        'insurance_user' => 'jijing,zhoujie,shenzhiliang,dujuan',
        'lvs' =>
            [
                0 => '192.168.1.118',
                1 => '192.168.1.119',
                2 => '192.168.1.120',
                3 => '192.168.1.128',
                4 => '192.168.1.129',
            ],
    ];
    /**
     * 发送钉钉报警
     */
    private static function sendDingDingAlert($msg,$userGroup=3){
        $alertList = self::getCloudConfig();
        $userGroups = array(
            1 => $alertList['user'],
            3 => $alertList['admire_shell_user'],
            9 => 'wuyunlin',	// 用来测试发送的
        );

        $user = $userGroups[$userGroup];
        $url = $alertList['url'];

        $data = array(
            'users' => $user,
            'content' => $msg,
        );
        $status = self::post3($url,$data);
    }


    /**
     * Description:Curl 模拟浏览器
     * @param string $url
     * @param string $curlPost
     * @param string $cookies
     * @param string $referer
     * @param string $userAgent
     * @param int $ifJson
     * @return mixed
     */
    private static function post3($url = '', $curlPost = '', $cookies = '',$referer='',$userAgent='',$ifJson=0)
    {
        $userAgent = $userAgent?$userAgent:(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "");
        $ch = curl_init();       //初始化curl
        if($ifJson){
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
            ));
        }

        curl_setopt($ch, CURLOPT_URL, $url);  //抓取指定网页
        // curl_setopt($ch, CURLOPT_HEADER, 0);  //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); //超时
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        if($curlPost){
            curl_setopt($ch, CURLOPT_POST, 1);   //post提交方式
            $curlPost = is_array($curlPost)?http_build_query($curlPost):$curlPost;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        }
        if($cookies){
            curl_setopt($ch, CURLOPT_COOKIE, self::joinCookie($cookies)); //使用cookies
        }

        if ($referer) {
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //返回最后的Location ,有redirect 会返回 重定向后的内容

        $data = curl_exec($ch);      //运行curl
        curl_close($ch);

        return $data;
    }

    /**
     * Description:开始运行
     * @param $data
     */
    public static function start($data)
    {
        $date = date("Y-m-d H:i",$data["happen_time"]);
        $error_msg = $date." ".$data["project_name"].":".$data["msg"];
        self::sendDingDingAlert($error_msg);
    }


    private static function getCloudConfig()
    {
        if(!self::$config) {
            $config = file_get_contents(self::$dingdingUrl);
            self::$config["response"] = json_decode($config)["response"];
        }
        return self::$config;
//        return self::$config["response"];
    }
}