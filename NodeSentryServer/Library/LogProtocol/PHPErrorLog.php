<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-10-31
 * Time: 下午7:38
 */

namespace Library\LogProtocol;

class PHPErrorLog implements ResolveProtocol {
    //这是php所有的错误等级
    static $php_error = [
        "Fatal error",
        "Recoverable fatal error",
        "Warning",
        "Parse error",
        "Notice",
        "Strict Standards",
        "Deprecated",
        "Unknown error"
    ];

    public static function parse($php_log)
    {
        return array_filter(explode("\n",$php_log));
    }

}