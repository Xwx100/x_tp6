<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/6 0006
// +----------------------------------------------------------------------

namespace x_tp6\services\event;


use think\Event;

/**
 * rpc 生命周期 日志
 * Class RpcCb
 *
 * @package x_tp6\event
 */
class RpcCb
{
    protected $name = null;
    protected $log = null;

    public function __construct(RpcCbName $name, RpcCbLog $log)
    {
        $this->name = $name;
        $this->log  = $log;
    }

    public function onMasterInit() {
    }

    public function onMasterStart() {
        $this->log->onMasterStart();
    }

    public function onMasterShutdown() {
        $this->log->onMasterShutdown();
    }

    public function onManagerStart() {
        $this->log->onManagerStart();
    }

    public function onWorkerStart() {
        $this->log->onWorkerStart();
    }

    public function onWorkerRpcConnect($args)
    {
        $this->log->onWorkerRpcConnect(...$args);
    }

    public function onWorkerRpcReceive($args)
    {
        $this->log->onWorkerRpcReceive(...$args);
    }

    public function onCoDispatcherBefore($args)
    {
        $this->log->onCoDispatcherBefore(...$args);
    }

    public function onCoDispatcherAfter($args)
    {
        $this->log->onCoDispatcherAfter(...$args);
    }

    public function onWorkerRpcClose($args)
    {
        $this->log->onWorkerRpcClose(...$args);
    }


    public function subscribe(Event $event)
    {
        foreach ($this->events() as $item) {
            x_app()->common->log(
                x_app()->common->sprintf('注册rpc事件：%s', $item)
            );
            $event->listen(...$item['args']);
        }
    }

    public function events(): array
    {
        return [
            ['args' => [$this->name->masterInit, [$this, 'onMasterInit']]], // 开启服务前
            ['args' => [$this->name->masterStart, [$this, 'onMasterStart']]],
            ['args' => [$this->name->masterShutdown, [$this, 'onMasterShutdown']]],

            ['args' => [$this->name->managerStart, [$this, 'onManagerStart']]],

            ['args' => [$this->name->workerStart, [$this, 'onWorkerStart']]],

            ['args' => [$this->name->workerRpcConnect, [$this, 'onWorkerRpcConnect']]],
            ['args' => [$this->name->workerRpcReceive, [$this, 'onWorkerRpcReceive']]],
            ['args' => [$this->name->coDispatcherBefore, [$this, 'onCoDispatcherBefore']]],
            ['args' => [$this->name->coDispatcherAfter, [$this, 'onCoDispatcherAfter']]],
            ['args' => [$this->name->workerRpcClose, [$this, 'onWorkerRpcClose'], false]],
        ];
    }
}