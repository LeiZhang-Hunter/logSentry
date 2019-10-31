<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-10-31
 * Time: 下午7:58
 */
namespace Structural\System;

class SwooleTcpStruct{
    const TCP_WorkerStart = "WorkerStart";
    const TCP_Receive = "Receive";
    const TCP_Task = "Task";
    const TCP_Finish = "Finish";
    const TCP_Close = "Close";
    const TCP_PipeMessage = "PipeMessage";
    const TCP_WorkerStop = "WorkerStop";
}