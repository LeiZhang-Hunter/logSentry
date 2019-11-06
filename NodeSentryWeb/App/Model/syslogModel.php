<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-4-16 0016
 * Time: 18:55
 */
class syslogModel extends Model
{
   public $table = "syslog";

    static $facility = [
        "kernel messages",
        "user-level messages",
        "mail system",
        "system daemons",
        "security/authorization messages [1]",
        "messages generated internally by syslogd",
        "line printer subsystem",
        "network news subsystem",
        "UUCP subsystem",
        "clock daemon [2]",
        "security/authorization messages [1]",
        "FTP daemon",
        "NTP subsystem",
        "log audit [1]",
        "log alert [1]",
        "clock daemon [2]",
        "local use 0 (local0)",
        "local use 1 (local1)",
        "local use 2 (local2)",
        "local use 3 (local3)",
        "local use 4 (local4)",
        "local use 5 (local5)",
        "local use 6 (local6)",
        "local use 7 (local7)"
    ];

    static $level = [
        LOG_EMERG=>"LOG_EMERG",
        LOG_ALERT=>"LOG_ALERT",
        LOG_CRIT=>"LOG_CRIT",
        LOG_ERR=>"LOG_ERR",
        LOG_WARNING=>"LOG_WARNING",
        LOG_NOTICE=>"LOG_NOTICE",
        LOG_INFO=>"LOG_INFO",
        LOG_DEBUG=>"LOG_DEBUG"
    ];

    const SEARCH_SYS = "sys";

    const SEARCH_PHP = "php";

    const SEARCH_MYSQL = "mysql";

    const SEARCH_NGINX = "nginx";

    static $type = [
        self::SEARCH_SYS,
        self::SEARCH_PHP,
        self::SEARCH_MYSQL,
        self::SEARCH_NGINX
    ];

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

    /**
     * @var \ElasticSearch\Client
     */
    private $es_client;

    private $count;

    public function __construct()
    {
        parent::__construct();
        if(!$this->es_client) {
            include_once __VENDOR___."/ElasticSearch/Auto.php";
            $this->es_client = \ElasticSearch\Client::connection(sprintf("http://%s:%s/%s/%s", config("ES")["ip"], config("ES")["port"], config("ES")["index"], config("ES")["type"]));
        }
    }

    /**
     * Description:通过ES搜索
     * @param array $condition
     * @param array $option
     * @return array
     */
    public function searchByEs($condition = [],$option = [])
    {
        $result = $this->es_client->search($condition,$option);
        $this->count = isset($result["hits"]["total"]) ? $result["hits"]["total"] : 0;

        $data = [];

        if(isset($result["hits"]["hits"]))
        {
            foreach ($result["hits"]["hits"] as $key=>$value)
            {
                $data[$key] = $value["_source"];
            }
        }
        return $data;
    }

    public function getCountByEs()
    {
        return $this->count;
    }

    public static function formatData($data)
    {
        if($data)
        {
            foreach ($data as $key=>$value)
            {
                $data[$key]["facility"] = self::$facility[$value["facility"]];
                $data[$key]["level"] = self::$level[$value["level"]];
                $data[$key]["php_error_level"] = self::$php_error[$value["php_error_level"]];
                $data[$key]["happen_time"] = $value["happen_time"] ? date("Y-m-d H:i",$value["happen_time"]) : "";
                $data[$key]["created_time"] = $value["created_time"] ? date("Y-m-d H:i",$value["created_time"]) : "";
                $data[$key]["type"] = self::$type[$value["type"]];
            }
        }
        return $data;
    }

    /**
     * Description:错误级别对应的色值
     * @param $error_level
     * @return string
     */
    public static function getErrorLevelColor($error_level)
    {
        $color = "#777777";
        switch ($error_level)
        {
            case "Fatal error":
                $color = "#4B0082";
                break;
            case "Recoverable fatal error":
                $color = "#CD5C5C";
                break;
            case  "Warning":
                $color = "#FF69B4";
                break;
            case "Parse error":
                $color = "#ADFF2F";
                break;
            case "Notice":
                $color = "#FFD700";
                break;
            case "Strict Standards":
                $color = "#7FFFD4";
                break;
            case "Deprecated":
                $color = "#00FFFF";
                break;
            case "Unknown error":
                $color = "#F0F8FF";
                break;
        }
        return $color;
    }

    /**
     * Description:错误级别对应的色值
     * @param $error_level
     * @return string
     */
    public static function getErrorLevelColorByNumber($error_level)
    {
        $color = "#777777";
        switch ($error_level)
        {
            case 0:
                $color = "#4B0082";
                break;
            case 1:
                $color = "#CD5C5C";
                break;
            case  2:
                $color = "#FF69B4";
                break;
            case 3:
                $color = "#ADFF2F";
                break;
            case 4:
                $color = "#FFD700";
                break;
            case 5:
                $color = "#7FFFD4";
                break;
            case 6:
                $color = "#00FFFF";
                break;
            case 7:
                $color = "#F0F8FF";
                break;
        }
        return $color;
    }

    //获取周日志
    public function getWeekLogCount()
    {
        $week_start = strtotime("this week Monday");
        $week_end = strtotime("this week Sunday")+24*60*60;
        $condition = [];

        $condition["query"]["bool"]["must"][] = [
            "range"=>[
                "created_time"=>[
                    "gte"=>$week_start
                ]
            ]
        ];

        $condition["query"]["bool"]["must"][] = [
            "range"=>[
                "created_time"=>[
                    "lt"=>$week_end
                ]
            ]
        ];

        $condition["size"] = 0;
        $this->searchByEs($condition);
        return $this->getCountByEs();
    }

    //获取月日志
    public function getMonthLogCount()
    {
        $month_start = mktime(0,0,0,date('m'),1,date('Y'));
        $month_end = mktime(23,59,59,date('m'),date('t'),date('Y'));

        $condition = [];

        $condition["query"]["bool"]["must"][] = [
            "range"=>[
                "created_time"=>[
                    "gte"=>$month_start
                ]
            ]
        ];

        $condition["query"]["bool"]["must"][] = [
            "range"=>[
                "created_time"=>[
                    "lt"=>$month_end
                ]
            ]
        ];

        $condition["size"] = 0;
        $this->searchByEs($condition);
        return $this->getCountByEs();
    }

    //获取总日志
    public function getAllCount()
    {
        $condition = [];
        $condition["size"] = 0;
        $this->searchByEs($condition);
        return $this->getCountByEs();
    }

    //用来获取服务器的日志数目
    public function formatListLogCount($client_list)
    {
        if($client_list)
        {
            foreach ($client_list as $key=>$client)
            {
                $condition = [];
                $condition["query"]["bool"]["must"][] = [
                    "match"=>[
                        "server_ip"=>$client["client_ip"]
                    ]
                ];
                $condition["size"] = 0;
                $this->searchByEs($condition);
                $client_list[$key]["log_count"] = $this->getCountByEs();
            }

            return $client_list;
        }else{
            return [];
        }
    }

}