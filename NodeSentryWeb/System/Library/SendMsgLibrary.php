<?php
/**
 * 发送短信
 * Class Flycorn_sendMsg
 */
class SendMsgLibrary extends Container
{
    protected $ci;
    function __construct()
    {
        $this->ci = &get_instance();
    }

    /**
     * 发送
     * @param $mobile //手机号
     * @param $content //内容
     */
    function send($mobile, $content)
    {
        require_once('http://180.153.97.200:8022/bridge/java/Java.inc');
        $result = ['status' => 0, 'msg' => '发送失败!'];

        @java_require('standerinfo-security-3.2.3.jar');

        $tmp1 = new Java('com.litt.core.security.ISecurityEncoder');
        $tmp2 = new Java('com.litt.core.security.SecurityException');
        $shgc = new Java('com.litt.core.security.SecurityFactory');

        $url = 'http://114.80.81.60:8081/sms-web/rest/sms/sendSms.json?';
        $appCode = 'Shgcpypt';
        $user = 'Shgcpypt';
        $password = 'Pypt816384';

        $source = 'mobile='.$mobile.'&content='.$content.'&appCode='.$appCode.'&username='.$user.'&password='.$password;

        $encoder = null;
        try{
            $encoder = $shgc->genRSAEncoder('//opt//soft//tomcat//webapps//bridge//WEB-INF//lib//Appcode-pri.key');
            $sign = $encoder->sign($source);
        }catch(JavaException $e){
            return $result;
        }
        //签名
        $sign = java_values($sign);
        $url = $url.'mobile='.$mobile.'&content='.$content.'&appCode='.$appCode.'&sign='.urlencode($sign);
        $send_result = file_get_contents($url);
        if(!empty($send_result)){
            $foo = json_decode($send_result, true);
            if(!empty($foo) && isset($foo['response']) && $foo['response']['success']){
                $result['status'] = 1;
                $result['msg'] = '发送成功!';
                return $result;
            }
        }
        return $result;
    }
}
