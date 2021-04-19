<?php
// +----------------------------------------------------------------------
// | 增加 日志
// +----------------------------------------------------------------------

namespace x_tp6\services\event;


use Swoole\Server;
use think\swoole\rpc\Protocol;
use think\swoole\RpcManager;
use think\swoole\Sandbox;

class RpcCbLog
{
    /**
     * @var RpcManager|null
     */
    protected $rpcManager = null;
    protected $sandbox = null;

    public function __construct(RpcManager $rpcManager, Sandbox $sandbox)
    {
        $this->rpcManager = $rpcManager;
        $this->sandbox = $sandbox;
    }

    public function getServer(): Server
    {
        return $this->rpcManager->getServer();
    }

    public function onMasterStart() {
        x_app()->common->log("主进程={$this->getServer()->getMasterPid()} 开始 ");
    }

    public function onMasterShutdown() {
        x_app()->common->log('主进程 关闭');
    }

    public function onManagerStart() {
        x_app()->common->log("管理进程={$this->getServer()->getManagerPid()} 开始");
    }

    public function onWorkerStart() {
        x_app()->common->log("工作进程={$this->getServer()->worker_pid} 开始");
    }

    public function onWorkerRpcConnect(Server $server, int $fd, int $reactorId)
    {
        $this->sandbox->getApplication()->debug("工作进程fd={$fd}reactorId={$reactorId} 开始连接");
        x_app()->common->log("工作进程fd={$fd}reactorId={$reactorId} 开始连接");

    }

    public function onWorkerRpcReceive(Server $server, $fd, $reactorId, $data)
    {
        x_app()->common->log("工作进程fd={$fd}reactorId={$reactorId} 开始接收");
    }

    // 已经替换成协程环境的app
    public function onCoDispatcherBefore(Protocol $protocol)
    {
        x_app()->env->_xRequestIdSet($protocol->getContext()['requestId']);
        x_app()->common->log("工作进程 开始协程调度");
        x_app()->common->log([$protocol->getParams(), $protocol->getContext()]);
    }

    // 已经替换成协程环境的app
    public function onCoDispatcherAfter($result)
    {
        x_app()->common->log($result);
        x_app()->common->log("工作进程 结束协程调度");
    }

    public function onWorkerRpcClose(Server $server, int $fd, int $reactorId)
    {
        x_app()->common->log("工作进程fd={$fd}reactorId={$reactorId} 关闭");
    }
}