<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/2 0002
// +----------------------------------------------------------------------

namespace x_tp6\services;


use app\change\x_admin\XAdmin;
use think\db\Query;
use think\swoole\RpcManager;
use think\swoole\Sandbox;
use x_tp6\services\change\Change;
use x_tp6\services\change\ChangeColumn;
use x_tp6\services\change\ChangeItem;
use x_tp6\services\change\ChangeJoin;
use x_tp6\services\change\ChangeRule;
use x_tp6\services\change\Name;
use x_tp6\services\event\RpcCbName;
use x_tp6\services\http_client\HttpClient;
use x_tp6\services\lang\Lang;
use x_tp6\services\common\Common;
use x_tp6\services\env\Env;
use x_tp6\services\trace\Trace;
use x_tp6\services\rpc\Client;

/**
 * 属于自己的容器
 * Class App
 *
 * @property Common     $common
 * @property Env        $env
 * @property Trace      $trace
 * @property httpClient $httpClient
 * @property Lang       $lang
 * @property Client     $rpcClient
 * @property RpcCbName  $eventRpcCbName
 * @property RpcManager $thinkRpcManager
 * @property Sandbox    $thinkSandbox
 * @property Name       $changeName
 * @method  ChangeJoin changeJoin
 * @method  ChangeItem changeItem
 * @method  ChangeRule changeRule
 * @method  Change change
 * @property \think\App $thinkApp
 * @method  Query   thinkQuery(array $vars = [], bool $newInstance = true)
 * @property XAdmin     $xAdmin
 * @package x_tp6
 */
class App
{
    protected $bind = [
        'common' => Common::class,
        'env' => Env::class,
        'trace' => Trace::class,
        'httpClient' => HttpClient::class,
        'lang' => Lang::class,
        'rpcClient' => Client::class,
        'eventRpcCbName' => RpcCbName::class,

        'change' => Change::class,
        'changeJoin' => ChangeJoin::class,
        'changeField' => ChangeColumn::class,
        'changeItem' => ChangeItem::class,
        'changeName' => Name::class,
        'changeRule' => ChangeRule::class,

        'thinkRpcManager' => RpcManager::class,
        'thinkSandbox' => Sandbox::class,
        'thinkApp' => \think\App::class,
        'thinkQuery' => Query::class,

        'xAdmin' => XAdmin::class
    ];

    public function __get($name)
    {
        return app()->make($this->bind[$name]);
    }

    public function __call($name, $arguments)
    {
        if (!isset($arguments[0])) {
            $arguments[0] = [];
        }
        if (!isset($arguments[1])) {
            $arguments[1] = true;
        }
        return app()->make($this->bind[$name], ...$arguments);
    }
}