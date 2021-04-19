<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/2 0002
// +----------------------------------------------------------------------

namespace x_tp6\services;



use think\Model;
use x_tp6\services\change\cmd\MakeTable;
use x_tp6\services\event\RpcCb;

class Services extends \think\Service
{

    /**
     * 绑定服务
     */
    public function register()
    {
        \think\facade\Db::event('before_select', function ($query) {
            return \x_tp6\bases\Model::onBeforeSelect($query);
        });
        \think\facade\Db::event('before_find', function ($query) {
            return \x_tp6\bases\Model::onBeforeFind($query);
        });
        $this->commands(MakeTable::class);
        Model::maker(\x_tp6\bases\Model::injectChangeTable());
    }

    /**
     * 容器App加载服务
     */
    public function boot()
    {
        x_app()->env->_xAppMake();
        x_app()->env->_xRequestIdMake();
        $method = x_app()->env->_xAppGet();
        x_app()->common->log("注册{$method}服务");
        if ($method) {
            call_user_func_array([$this, 'boot' . $method], []);
        }
    }

    public function bootRpc() {
        x_app()->common->log('加载rpc事件');
        $this->app->event->subscribe(RpcCb::class);
    }

    public function bootHttp() {
        x_app()->common->log('加载rpc客户端');
        x_app()->rpcClient->injectServices();
    }

    public function bootCli() {

    }
}