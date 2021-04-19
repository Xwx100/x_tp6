<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/6 0006
// +----------------------------------------------------------------------

namespace x_tp6\services\lang\driver;


use think\helper\Arr;
use think\helper\Str;

class Baidu
{
    protected $config = [
        'api' => '',
        'api_params' => [
            'app_id' => '',
            'app_secret' => '',
            // 翻译源语言
            'from' => 'auto',
            // 翻译目标语言
            'to' => 'auto'
        ],
    ];

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    public function run($en)
    {
        $params = array_merge(['q' => $en], $this->config['api_params']);
        $params['appid'] = $params['app_id'];
        $params['salt'] = Str::random();
        $params['sign'] = md5(implode('', [$params['appid'], $params['q'], $params['salt'], $params['app_secret']]));

        unset($params['app_id']);
        unset($params['app_secret']);


        $res = x_app()->httpClient->post(
            $this->config['api'],
            [
                x_app()->httpClient->keyForm => $params
            ]
        );

        $res = json_decode($res->getBody(), true);
        return Arr::get($res, 'trans_result.0.dst', '');
    }
}