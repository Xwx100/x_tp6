<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/15 0015
// +----------------------------------------------------------------------

namespace x_tp6\services\change;


use x_tp6\services\change\interfaces\Table;
use x_tp6\traits\ArrayAccessProp;

/**
 * Class ChangeJoin
 *
 * @package x_tp6\services\change
 */
class ChangeJoin implements \ArrayAccess
{
    use ArrayAccessProp;

    /**
     * @var Table
     */
    public $tableObj = null; // 必须 表对象
    public $tableObjClass = null; // 必须 表类名

    public $join = null;
    public $on = null;
    public $type = null;
    public $as = null;
    /**
     * @var array|callable
     */
    public $trigger;

    public function leftJoin(Table $join): ChangeJoin
    {
        return $this->join($join)->type('left');
    }

    public function rightJoin(Table $join): ChangeJoin
    {
        return $this->join($join)->type('right');
    }


    public function join(Table $join): ChangeJoin
    {
        $this->tableObj = $join;
        $this->tableObjClass = $join->getClass();
        $this->join = $join->table;
        return $this;
    }

    public function as($as): ChangeJoin
    {
        $this->as = $as;
        return $this;
    }

    public function getTable(): string
    {
        $table = $this->join;
        if ($this->as) {
            $table = "{$table} {$this->as}";
        }
        return $table;
    }

    public function on($on): ChangeJoin
    {
        $this->on = $on;
        return $this;
    }

    public function type($type): ChangeJoin
    {
        $this->type = $type;
        return $this;
    }

    public function trigger(callable $trigger)
    {
        $this->trigger = $trigger;
        return $this;
    }

    public function toModelJoin(): array
    {
        return [$this->join, $this->on, $this->type];
    }
}