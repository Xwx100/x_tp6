<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/6 0006
// +----------------------------------------------------------------------

namespace x_tp6\services\http_client;


use GuzzleHttp\Client;

/**
 * Class HttpClient
 *
 * @mixin Client
 * @package x_tp6\http_client
 */
class HttpClient
{
    // 拼接url
    public $keyQuery = 'query';
    // 表单
    public $keyForm = 'form_params';

    public function __call($name, $arguments)
    {
        return call_user_func_array([app(Client::class), $name], $arguments);
    }
}