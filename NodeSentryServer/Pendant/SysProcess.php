<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2019/1/8
 * Time: 12:43
 */
namespace Pendant;
class SysProcess
{
    //前置信号处理
    public static function beforeHook()
    {
        pcntl_signal(SIGCHLD,function($signo){
            var_dump($signo);
        });
        pcntl_signal(SIGCHLD, "sig_handler");
        pcntl_signal(SIGTERM,"sig_handler");
    }

    /**
     * Description:创建守护进程
     */
    public static function createProcess($name,$function)
    {
        $pid = pcntl_fork();
        if($pid == 0)
        {//子进程
            cli_set_process_title($name);
            $function();
        }else if($pid < 0)
        {
            exit("域套接字创建失败");
        }
    }

}