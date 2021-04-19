<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/8 0008
// +----------------------------------------------------------------------

namespace x_tp6\interfaces\rpc;


use think\swoole\rpc\Protocol;

interface Middleware
{
    public function handle(Protocol $protocol, $next);
}