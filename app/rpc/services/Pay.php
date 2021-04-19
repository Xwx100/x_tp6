<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------

namespace app\rpc\services;


class Pay implements \app\rpc\interfaces\Pay
{
    public function run($money)
    {
        x_app()->common->log(1111111);
        x_app()->common->log(2222222);

        return ['money' => $money];
    }
}