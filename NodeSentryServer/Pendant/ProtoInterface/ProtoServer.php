<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 19-9-18
 * Time: 下午7:23
 */
namespace Pendant\ProtoInterface;
interface ProtoServer{

    public function bindWorkerStart(...$args);

    public function bindTask(...$args);

    public function bindReceive(...$args);

    public function bindPipeMessage(...$args);

    public function bindFinish(...$args);

    public function bindClose(...$args);

}