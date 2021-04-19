<?php

use app\ExceptionHandle;
use app\Request;


// 容器Provider定义文件
return [
    'think\Request' => Request::class,
    'think\exception\Handle' => ExceptionHandle::class,
    'log' => \x_tp6\providers\Log::class,
    \think\swoole\RpcManager::class => \x_tp6\providers\rpc\RpcManager::class,
    \think\Db::class => \x_tp6\providers\Db::class
];
