<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-11-4
 * Time: 下午6:00
 */
class nodeSentryModel extends Model{
    public $table = "node_sentry";

    const F_id="id",F_open_state="open_state",F_state = "state",F_type="type",F_server_id="server_id";

    const F_open=1,F_close=0;

    const F_client = 0,F_server=1;

    const F_type_arr = [
        1=>"日志哨兵",
    ];

    /**
     * 根据server_id获取哨兵的配置信息
     * @param $sentry_id
     * @return array
     */
    public function getSentryInfoBySentryId($sentry_id)
    {
        if(!$sentry_id)
        {
            return [];
        }

        try {
            $res = $this->where(self::F_id, $sentry_id)->where(self::F_state,1)->getInfo();
        }catch (\Exception $exception)
        {
            return [];
        }
        return $res ? $res : [];
    }

    public function getListByServerId($server_id)
    {
        if(!$server_id)
        {
            return [];
        }

        try {
            $res = $this->where(self::F_server_id, $server_id)->where(self::F_state,1)->search();
        }catch (\Exception $exception)
        {
            return [];
        }
        return $res ? $res : [];
    }

    //改变哨兵的状态
    public function changeSentryState($server_id,$state)
    {
        if(!$server_id)
        {
            return false;
        }

        try {
            $res = $this->where(self::F_id, $server_id)->modify([self::F_open_state => $state]);
        }catch (\Exception $exception)
        {
            return false;
        }
        return $res ? $res : false;
    }

    //获取列表
    public function getList($ip)
    {
        if($ip)
        {
            $this->where("server_ip",$ip);
        }

        $this->where("state",-1,">");
        $this->orderBy("created_time","desc");
        $list = $this->search();
        return $list ? $list : [];
    }

    //检查ip地址
    public function checkClientIp($id,$client_ip)
    {
        $result = $this->where("id",$id,"!=")->where("state",-1,">")->where("client_ip",$client_ip)->getInfo();
        return $result;
    }

    //制作token
    public function makeToken()
    {
        return md5(uniqid());
    }
}