<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-10-31
 * Time: 下午7:40
 */
namespace Library\LogProtocol;
class SysLogProResolve implements ResolveProtocol {

    //拆析协议
    public static function parse($text,$type,$split = "")
    {
        $BSDtext = trim($text);

        //拆分协议PRI头，这个数字是由Facility乘以 8，然后加上Severity得来，消息体<>之间
        $pri_end_position = strpos($BSDtext,">");
        if($pri_end_position === false)
            return false;
        $pri = substr($BSDtext,1,$pri_end_position > 1 ? $pri_end_position-1 : 0);
        if(!is_numeric($pri))
        {
            return false;
        }
        //获取设施
        $facility = intval(floor($pri/8));

        //获取严重性
        $severity = $pri%8;

        //剩余不必要的消息放在一起
        $body = substr($BSDtext,$pri_end_position+1);
        if(!$body)
        {
            return false;
        }

        $syslog_array = explode(" ",$body);//过滤掉空格
        $syslog_array = array_filter($syslog_array,function($var){
            return $var ? true : false;
        });

        //月
        $month = isset($syslog_array[0]) ? array_shift($syslog_array) : '';
        if(!$month)
            return false;

        //日
        $day = isset($syslog_array[0]) ? array_shift($syslog_array) : '';
        if(!$day)
            return false;

        //时分秒
        $time = isset($syslog_array[0]) ? array_shift($syslog_array) : '';
        if(!$time)
            return false;

        $hostname = isset($syslog_array[0]) ? array_shift($syslog_array) : '';
        if(!$hostname)
            return false;

        $body = trim(implode(" ",$syslog_array));

        $happen_time = strtotime($month." ".$day." ".$time);
        if(!$happen_time)
            return false;

        $data["facility"] = $facility;
        $data["severity"] = $severity;
        $data["hostname"] = $hostname;
        $data["happen_time"] = $happen_time;
        $data["body"] = $body;


        return $data;
    }
}