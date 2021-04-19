<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/8 0008
// +----------------------------------------------------------------------

namespace x_tp6\services\middleware\rpc;


use think\swoole\rpc\Protocol;
use x_tp6\interfaces\rpc\Middleware;

/**
 * http 调用 rpc 发送 requestId
 * Class RequestId
 *
 * @package x_tp6\services\middleware\rpc
 */
class RequestId implements Middleware
{
    public function handle(Protocol $protocol, $next)
    {
        $protocol->setContext(array_merge(
            ['requestId' => x_app()->env->_xRequestIdMake()],
            $protocol->getContext()
        ));

        return $next($protocol);
    }
}