<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------

namespace x_tp6\providers\rpc;


use Swoole\Server;

/**
 * 新增 工作进程接收信息回调事件
 * Class RpcManager
 *
 * @package app\rpc\providers
 */
class RpcManager extends \think\swoole\RpcManager
{

    protected function prepareRpcClient()
    {
        // http不是swoole环境 暂不需要
    }

    public function onReceive(Server $server, $fd, $reactorId, $data)
    {
        x_app()->thinkSandbox->getBaseApp()->event->trigger(
            x_app()->eventRpcCbName->workerRpcReceive,
            func_get_args()
        );
        parent::onReceive($server, $fd, $reactorId, $data);
    }
}