<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/4 0004
// +----------------------------------------------------------------------

namespace x_tp6\services\common;


/**
 * 通用类
 * Class Common
 *
 * @package x_tp6\services\common
 */
class Common
{
    /**
     * 抛出异常
     *
     * @param $msg
     * @param $code
     * @param $previous
     * @throws \Exception
     */
    public function exception($msg = '', $code = 0, $previous = null)
    {
        throw new \Exception($msg, $code, $previous);
    }

    /**
     * 格式化
     *
     * @param       $format
     * @param mixed ...$args
     * @return string
     */
    public function sprintf($format, ...$args): string
    {
        foreach ($args as &$arg) {
            if (is_scalar($arg)) {
                continue;
            }
            if (is_object($arg)) {
                $arg = method_exists($arg, '__toString') ? $arg : get_class($arg);
            } elseif (is_array($arg)) {
                $arg = json_encode($arg, JSON_UNESCAPED_UNICODE);
            }
        }

        return sprintf($format, ...$args);
    }

    /**
     * 打印日志
     *
     * @param        $msg
     * @param string $type
     * @param array  $context
     * @param bool   $lazy
     * @return \think\Log
     */
    public function log($msg, string $type = 'info', array $context = [], bool $lazy = true)
    {
        // 代理异常
        if ($msg instanceof \Exception) {
            $traceString = str_replace(["\n"], ["\n\t"], $msg->getTraceAsString());
            $msg = "\n\t[file={$msg->getFile()}:{$msg->getLine()} code={$msg->getCode()} msg={$msg->getMessage()}]: 抛出异常 \n\t{$traceString}";
        }

        if (is_string($msg)) {
            // 禁止嵌套 防止打印日志位置错误
            $pos = x_app()->trace->injectLast(2)->run();
            $pos = implode(' - ', $pos);
            $msg = " [{$pos}] {$msg} ";
        }

        return app()->log->record($msg, $type, $context, $lazy);
    }

    public function getVarUuid($var)
    {
        $varId = null;
        if (is_object($var)) {
            $varId = spl_object_hash($var);
        } else {
            $varId = md5(serialize($var));
        }

        $this->log($this->sprintf("%s 生成变量唯一ID={$varId}", $var));
        return $varId;
    }

    /**
     * 分表规则
     * @param     $str
     * @param int $max
     * @return string
     */
    public function subTable($str, $max = 100): string
    {
        $h = base_convert(md5($str), 16, 10);
        $zero = strlen($max) - 1;
        return sprintf("%0{$zero}d", fmod($h, $max));
    }

    /**
     * 需要时再加载实例化
     *
     * @param $value
     * @return \Closure
     */
    public function closure($value)
    {
        return function () use ($value) {
            return $value;
        };
    }

    /**
     * @return Arr
     */
    public function arr()
    {
        return app()->make(Arr::class);
    }

    /**
     * @return Str
     */
    public function str()
    {
        return app()->make(Str::class);
    }
}