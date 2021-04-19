<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/19 0019
// +----------------------------------------------------------------------

namespace x_tp6\services\change;


use think\helper\Arr;
use x_tp6\traits\ArrayAccessProp;

/**
 * Class ChangeItem
 *
 * @package x_tp6\services\change
 */
class ChangeItem implements \ArrayAccess
{
    use ArrayAccessProp;

    public $field = [];
    public $join = [];
    public $where = [];
    public $group = [];
    public $order = [];
    public $having = [];
    public $page = [];

    public function run(array $item)
    {
        foreach ($this as $k => $v) {
            $v = Arr::get($item, $k, []);
            if (!empty($v)) {
                $this->$k = $v;
            }
        }
    }
}