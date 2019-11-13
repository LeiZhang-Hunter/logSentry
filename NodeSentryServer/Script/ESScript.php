<?php
/**
 * Description:这个脚本是用来清除索引的
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/18
 * Time: 16:53
 */
define("ENV","develop");
include_once "../autoload.php";

$es = new \Vendor\ES(\Pendant\SysConfig::getInstance()->getSysConfig("es"));
$es->client->delete();
$result = $es->createIndex("syslog","syslog",[
    "id"=>[
        "type" => "long",
        "store" => false,
    ],
    "sentry_type"=>[
        "type" => "integer",
        "store" => false
    ],
    "sentry_file"=>[
        "type" => "keyword",
        "store" => false
    ],
    "client_ip"=>[
        "type" => "keyword",
        "store" => false
    ],
    "happen_time"=>[
        "type" => "integer",
        "store" => false
    ],
    "body"=>[
        "type" => "keyword",
        "store" => true
    ],
    "php_error_level"=>[
        "type" => "integer",
        "store" => false
    ],
    "created_time"=>[
        "type" => "integer",
        "store" => false
    ],
    "updated_time"=>[
        "type" => "integer",
        "store" => false
    ],
    "state"=>[
        "type" => "integer",
        "store" => false
    ],
    "type"=>[
        "type" => "integer",
        "store" => false
    ],
]);
var_dump($result);