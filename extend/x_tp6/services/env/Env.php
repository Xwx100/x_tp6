<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/4 0004
// +----------------------------------------------------------------------

namespace x_tp6\services\env;


use x_tp6\traits\PropManage;

/**
 * 环境
 * Class Env
 * @method string _xAppSetRpc
 * @method string _xAppSetHttp
 * @method string _xAppEqualHttp
 * @method string _xAppEqualRpc
 * @method string _xAppEqualCli
 * @method string _xAppMakeRpc
 * @method string _xAppMakeHttp
 * @method string _xAppMake
 * @method string _xAppGet
 * @method string _xRequestIdMake(string $requestId = null)
 * @method string _xRequestIdSet(string $requestId = null)
 *
 * @package x_tp6\services\env
 */
class Env
{
    const RPC = 'Rpc';
    const CLI = 'Cli';
    const HTTP = 'Http';
    const RPC_CLI_COMMAND = 'swoole:rpc';

    use PropManage;

    protected $app = null;
    protected $requestId = null;

    public function app()
    {
        if (app()->request->isCli()) {
            if (!empty($_SERVER['argv']) && $_SERVER['argv'][1] === self::RPC_CLI_COMMAND) {
                $app = self::RPC;
            } elseif (!empty($_SERVER['argv']) && $_SERVER['argv'][1] === 'run') {
                $app = self::HTTP;
            } else {
                $app = self::CLI;
            }
        } else {
            $app = self::HTTP;
        }

        x_app()->common->log("加载环境变量app={$app}");
        return $app;
    }

    public function requestId($requestId = null)
    {
        if ($this->_xAppEqualHttp() || $this->_xAppEqualCli()) {
            $requestId = substr(md5(uniqid(rand(0, 100000))), 0, 16);
        } elseif ($this->_xAppEqualRpc()) {

        }

        return $requestId;
    }
}