<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/8 0008
// +----------------------------------------------------------------------

namespace x_tp6\services\middleware\rpc;


use think\swoole\rpc\Protocol;
use x_tp6\interfaces\rpc\Middleware;

class Dispatcher implements Middleware
{
    public function handle(Protocol $protocol, $next)
    {
        x_app()->thinkSandbox->getBaseApp()->event->trigger(
            x_app()->eventRpcCbName->coDispatcherBefore,
            [$protocol]
        );

        $res = $next($protocol);

        x_app()->thinkSandbox->getBaseApp()->event->trigger(
            x_app()->eventRpcCbName->coDispatcherAfter,
            [$res]
        );

        return $res;
    }
}