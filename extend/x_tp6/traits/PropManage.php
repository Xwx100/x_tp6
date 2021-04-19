<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/4 0004
// +----------------------------------------------------------------------

namespace x_tp6\traits;

/**
 * 不兼容 非驼峰式写法
 * Trait PropManage
 * @package x_tp6\traits
 */
trait PropManage
{
    public function __call($name, $arguments)
    {
        $preName = substr($name, 0, 2);
        // _xAppSetRpc
        if (method_exists($this, $preName)) {
            return call_user_func([$this, $preName], substr($name, 2), $arguments);
        }
        return null;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function _x($name, $arguments)
    {
        $triggers = $this->triggers();
        // _xAppSetRpc
        foreach ($triggers as $trigger) {
            $triggerOffset = strpos($name, ucfirst($trigger));
            if (false !== $triggerOffset) {
                // 大驼峰 => 小驼峰
                $key = substr($name, 0, $triggerOffset);
                // 大驼峰 => 小驼峰
                $method = lcfirst($trigger);
                $value = substr($name, $triggerOffset + strlen($trigger));

                $key = lcfirst($key);
                if (empty($value)) {
                    $value = $arguments;
                }
                if ($value) {
                    $value = (array)$value;
                }
                return call_user_func([$this, $method], $key, ...$value);
            }
        }
    }

    /**
     * 小驼峰写法
     * @return string[]
     */
    public function triggers(): array
    {
        return ['equal', 'set', 'get', 'make'];
    }

    /**
     * @param $name
     * @param $value
     * @return bool
     * @throws \Exception
     */
    public function equal($name, $value): bool
    {
        return $this->get($name) === $value;
    }

    public function set($name, $value)
    {
        $this->$name = $value;
        return $this;
    }

    public function get($name)
    {
        return $this->$name ?? null;
    }

    /**
     * 根据是否为null值 判断 是否只定义一次
     *
     * @param       $name
     * @param array $arguments
     * @return mixed
     */
    public function make($name, ...$arguments)
    {
        if (is_null($this->get($name))) {
            if (is_string($arguments)) {
                // 调用原值
                $this->set($name, $arguments);
            } else {
                // 默认调用 属性名方法
                if (method_exists($this, $name)) {
                    $this->set($name, call_user_func_array([$this, $name], $arguments));
                }
            }
        }
        return $this->get($name);
    }
}