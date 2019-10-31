<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-10-24
 * Time: 下午10:21
 */
namespace Pendant\Common;
class Tool{
    public static function ntohll($netData)
    {
        $number = 0x0000;
        $number = ($number << 8)|bin2hex($netData[0]);
        $number = ($number << 8)|bin2hex($netData[1]);
        $number = ($number << 8)|bin2hex($netData[2]);
        $number = ($number << 8)|bin2hex($netData[3]);
        $number = ($number << 8)|bin2hex($netData[4]);
        $number = ($number << 8)|bin2hex($netData[5]);
        $number = ($number << 8)|bin2hex($netData[6]);
        $number = ($number << 8)|(bin2hex(($netData[7])));
        return hexdec($number);
    }
}