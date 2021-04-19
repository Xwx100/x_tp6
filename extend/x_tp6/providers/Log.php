<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/6 0006
// +----------------------------------------------------------------------

namespace x_tp6\providers;

/**
 * 增加 请求唯一ID
 * Class MyLog
 *
 * @package x_tp6\providers
 */
class Log extends \think\Log
{

    public function record($msg, string $type = 'info', array $context = [], bool $lazy = true)
    {
        // 由于xdebug特性 不放在这
        if (is_scalar($msg)) {
            $requestId = x_app()->env->_xRequestIdMake();
            if ($requestId) {
                $msg = " [$requestId] {$msg} ";
            }
        }
        return parent::record($msg, $type, $context, $lazy);
    }
}