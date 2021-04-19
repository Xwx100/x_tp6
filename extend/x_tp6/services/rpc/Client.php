<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/7 0007
// +----------------------------------------------------------------------

namespace x_tp6\services\rpc;

use think\App;
use think\helper\Arr;
use think\swoole\rpc\client\Gateway;
use think\swoole\rpc\client\Proxy;
use think\swoole\rpc\JsonParser;

/**
 * http客户端
 * 加载rpc实例 模拟InteractsWithRpcClient
 * Class Instances
 *
 * @package x_tp6\services
 */
class Client
{
    protected $rpcServices = [];

    public function getServices() {
        //引入rpc接口文件
        if (file_exists($rpc = app()->getBasePath() . 'rpc.php')) {
            $this->rpcServices = (array) include $rpc;
        }
        return $this->rpcServices;
    }

    public function injectServices() {
        // 绑定rpc接口
        // rpc\contract => rpc\service use proxy
        try {
            foreach ($this->getServices() as $name => $abstracts) {
                $client = config("swoole.rpc.client.{$name}");
                $parserClass = Arr::get($client, 'parser', JsonParser::class);
                $parser      = app()->make($parserClass);
                $gateway     = new Gateway($client, $parser);
                $middleware  = Arr::get($client, 'middleware', []);

                x_app()->common->log("注册rpc客户端={$name}");

                foreach ($abstracts as $abstract) {
                    app()->bind($abstract, function (App $app) use ($middleware, $gateway, $name, $abstract) {
                        return $app->invokeClass(Proxy::getClassName($name, $abstract), [$gateway, $middleware]);
                    });
                }
            }
        } catch (\Exception $e) {
            x_app()->common->log($e);
        }
    }
}