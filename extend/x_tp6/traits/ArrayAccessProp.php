<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/11 0011
// +----------------------------------------------------------------------

namespace x_tp6\traits;

/**
 * Trait Table
 * @package x_tp6\services\change\rule
 */
trait ArrayAccessProp
{

    public function offsetGet($offset)
    {
        return $this->$offset ?? null;
    }

    public function offsetExists($offset)
    {
        return empty($this->$offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    public function offsetUnset($offset)
    {
        $this->$offset = null;
    }
}