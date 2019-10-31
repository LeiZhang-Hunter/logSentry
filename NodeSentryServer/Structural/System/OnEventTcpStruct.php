<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-10-31
 * Time: 下午8:05
 */
namespace Structural\System;

class OnEventTcpStruct{
    const ON_bindWorkerStart = "bindWorkerStart";
    const ON_bindReceive = "bindReceive";
    const ON_bindTask = "bindTask";
    const ON_bindFinish = "bindFinish";
    const ON_bindClose = "bindClose";
    const ON_bindPipeMessage = "bindPipeMessage";
    const ON_bindWorkerStop = "bindWorkerStop";
}