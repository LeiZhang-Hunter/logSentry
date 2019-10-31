<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/18
 * Time: 13:16
 */
namespace Vendor;

use ElasticSearch\Client;

class ES{

    private $config;

    /**
     * @var Client
     */
    public $client;

    public function __construct($config)
    {
        //加载es库
        include_once __ROOT__ . "/Vendor/ElasticSearch/Auto.php";
        $this->config = $config;
        $this->client = Client::connection(sprintf("http://%s:%s/%s/%s", $config["ip"], $config["port"], $config["index"], $config["type"]));
    }


    /**
     * es 会自动创建索引
     *
     * @param $index
     * @param $type
     * @param $properties
     * @return mixed
     */
    function createIndex($index, $type, $properties)
    {
        $url = sprintf("http://%s:%s/%s", $this->config["ip"], $this->config["port"], $index);
        $mappings = [];
        $mappings[$type]['properties'] = $properties;
        $data['mappings'] = $mappings;
        $data = json_encode($data, JSON_FORCE_OBJECT);
        return $this->put($url, $data);
    }

    /**
     * put 请求 必须有id
     *
     * @param $url
     * @param $data
     * @return mixed
     */
    function put($url, $data)
    {
        $result = $this->curl("PUT", $url, $data);
        return $result;
    }

    function curl($type, $url, $data, $header = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); //定义请求地址
        if (strtolower($type) != "post" && strtolower($type) != "get") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        }
        if (strtolower($type) == "put") {
            curl_setopt($ch, CURLOPT_HTTPHEADER,
                array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));
        }
        if (strtolower($type) == "post") {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        curl_setopt($ch, CURLOPT_HEADER, 0); //定义是否显示状态头 1：显示 ； 0：不显示
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//定义header
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//定义是否直接输出返回流
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //定义提交的数据
        }
        $res = curl_exec($ch);
        curl_close($ch);//关闭
        return $res;
    }
}