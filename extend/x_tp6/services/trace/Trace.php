<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/5 0005
// +----------------------------------------------------------------------
namespace x_tp6\services\trace;

use think\helper\Arr;

/**
 * 取倒数第last个trace
 * Class Trace
 *
 * @package x_tp6\services\trace
 */
class Trace
{
    public $last = 1;
    public $max = 4;

    /**
     * @var array 忽视函数名
     */
    public $ignore = ['__call', '__callStatic'];

    public function run($only = ['file', 'line']): array
    {
        $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $traces = array_slice($traces, $this->last - 1, $this->last + $this->max);
        $count = 0;
        do {
            $trace = array_shift($traces);
            $count++;
        } while ($count < $this->last && isset($trace['line']) && in_array($trace['line'], $this->ignore));
        return Arr::only((array)$trace, $only);
    }

    public function injectLast($last = 1): Trace
    {
        $this->last = $last;
        return $this;
    }
}