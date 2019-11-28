<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-11-15
 * Time: 下午2:25
 */

namespace Library\LogProtocol;

use Library\Logger\Logger;
use Vendor\DB;
use Vendor\ES;

class NginxLog implements ResolveProtocol {
    /**
     * @var Logger
     */
    private static $logger;

    /**
     * @var DB
     */
    private static $db;

    /**
     * @var ES
     */
    private static $es;

    public static function ModuleInit($logger,$db,$es)
    {
        self::$logger = $logger;
        self::$db = $db;
        self::$es = $es;
    }

    public static function parse($log,$sentry_type)
    {
        var_dump($log);
        var_dump($sentry_type);
    }

}