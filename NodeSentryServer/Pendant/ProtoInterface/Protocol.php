<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-9-18
 * Time: 下午7:54
 */
namespace Pendant\ProtoInterface;
interface Protocol{
    public static function parse($text);
}