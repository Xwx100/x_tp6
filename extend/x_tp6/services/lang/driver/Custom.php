<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/6 0006
// +----------------------------------------------------------------------

namespace x_tp6\services\lang\driver;


/**
 * 自定义语言包
 * Class Custom
 *
 * @package x_tp6\lang\driver
 */
class Custom
{
    protected $enZh = [
        'login fail' => '登录 失败'
    ];

    public function run($en)
    {
        return $this->enZh[$en] ?? '';
    }

    public function __get($name)
    {
        return $this->run($name);
    }
}