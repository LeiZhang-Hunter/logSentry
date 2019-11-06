<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17-11-10
 * Time: 下午3:13
 */
class RedisCore{

    private $redisObject;
    private $ci;

    public function __construct()
    {
        static $redisResult;
        $this->ci = & get_instance();
        if(!$redisResult)
        {
            $redisResult =  new Redis();
            $host = $this->ci->config->item('host');
            $port = $this->ci->config->item('port');

            if(!$host || !$port)
                throw new Exception('please input config');
            $redisResult->connect($host,$port);
            $this->redisObject = $redisResult;
        }else{
            $this->redisObject = $redisResult;
        }
    }


    public function setListData($list,$value)
    {
        $result = $this->redisObject->lPush($list,$value);
        return $result;
    }

    public function getListData($list,$begin,$end)
    {
        $result = $this->redisObject->lrange($list,$begin,$end);
        return $result;
    }
    public function set($key,$value) {
        $result = $this->redisObject->set($key,$value);
        if((int)$this->ci->config->item('timeout') != 0)
            $this->redisObject->expire($key,$this->ci->config->item('timeout'));
        return $result;
    }

    public function get($key) {
        $result = $this->redisObject->get($key);
        return $result;
    }

    public function delete($key)
    {
        $result = $this->redisObject->del($key);
        return $result;
    }

    public function updateListData($list,$key,$value)
    {
        $dataArr = $this->getListData($list,0,-1);
        if(!$dataArr)
        {
            return false;
        }
        $this->deleteList($list,0,0);
        $this->redisObject->lPop($list);
        foreach ($dataArr as $key=>$redisValue)
        {
            $data = redisDecode($redisValue);
            if($data['id'] == $key)
            {
                $data = $value;
            }
            $this->setListData($list,redisEncode($data));
        }
        $result = $this->redisObject->lSet($list,$key,$value);
        return $result;
    }

    public function deleteList($list,$key,$value)
    {
        $this->redisObject->ltrim($list,$key,$value);
    }

    public function deleteAll()
    {
        return $this->redisObject->flushAll();
    }
}