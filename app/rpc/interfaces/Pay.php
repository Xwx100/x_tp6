<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------

namespace app\rpc\interfaces;


interface Pay
{
    public function run($money);
}