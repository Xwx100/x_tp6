<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/19 0019
// +----------------------------------------------------------------------

namespace x_tp6\services\change;


use x_tp6\traits\ArrayAccessProp;

trait ChangeRule
{
    use ArrayAccessProp;

    public $commonField = [];
    public $field = [];
    public $fieldJoin = [];
    public $fieldWhere = [];
    public $fieldGroup = [];
    public $fieldOrder = [];
    public $fieldHaving = [];
    /**
     * @var ChangeJoin[]
     */
    public $join = [];
    public $where = [];
    public $group = [];
    public $order = [];
    public $having = [];
    public $page = [];

    public function handleFieldRule($rule, $key): string
    {
        if (empty($rule)) {
            return '';
        }
        if (is_scalar($rule)) {
            return $rule;
        } elseif (is_callable($rule)) {
            return call_user_func($rule, $key);
        }
        return '';
    }


    /**
     * @param $class
     * @return ChangeJoin
     */
    public function getJoinRule($class): ChangeJoin
    {
        return $this->join[$class];
    }

    public function getWhereRule($key)
    {
        return $this->where[$key];
    }

    public function getFieldRule($key)
    {
        return $this->getFieldSceneRule($key, 'field');
    }

    public function getFieldWhereRule($key)
    {
        return $this->getFieldSceneRule($key, 'fieldWhere');
    }

    public function getFieldJoinRule($key)
    {
        return $this->getFieldSceneRule($key, 'fieldJoin');
    }

    public function getFieldGroupRule($key)
    {
        return $this->getFieldSceneRule($key, 'fieldGroup');
    }

    public function getFieldOrderRule($key)
    {
        return $this->getFieldSceneRule($key, 'fieldOrder');
    }

    public function getFieldSceneRule($key, $scene = 'field')
    {
        if (empty($key)) {
            return array_merge($this->commonField, $this->$scene);
        }
        return x_app()->common->arr()->get($this->$scene, $key) ?: x_app()->common->arr()->get($this->commonField, $key);
    }

    public function whereRule($key, $cb = null): self
    {
        if (empty($key)) {
            return $this;
        }
        if (empty($cb)) {
            $cb = function ($value, $point) {
                return [$point, '=', $value];
            };
        } elseif ($cb === 'in') {
            $cb = function ($value, $point) {
                return [$point, 'in', $value];
            };
        } elseif ($cb === 'left_like') {
            $cb = function ($value, $point) {
                return [$point, 'like', "%{$value}"];
            };
        } elseif ($cb === 'right_like') {
            $cb = function ($value, $point) {
                return [$point, 'like', "{$value}%"];
            };
        } elseif ($cb === 'like') {
            $cb = function ($value, $point) {
                return [$point, 'like', "%{$value}%"];
            };
        } elseif (in_array($cb, ['date', 'datetime'])) {
            $cb = function ($value, $point) use ($cb) {
                $c = count($value);
                if (empty($value) || !is_array($value) || $c != 2) {
                    return [];
                }
                $fill = ($cb === 'datetime');
                if (in_array($value[0], ['>', '<', '>=', '<='])) {
                    if ($fill && $value[1] === '>=') {
                        $value[1] = "{$value[1]} 00:00:00";
                    } elseif ($fill && $value[1] === '<=') {
                        $value[1] = "{$value[1]} 23:59:59";
                    }

                    return [$point, $value[0], $value[1]];
                } else {
                    // = 2021
                    if ($value[0] === '=') {
                        $value = ["{$value[1]} 00:00:00", "{$value[1]} 23:59:59"];
                    } elseif ($fill) {
                        $value = ["{$value[0]} 00:00:00", "{$value[1]} 23:59:59"];
                    }
                    return [$point, 'between', $value];
                }
            };
        }

        $key = (array)$key;
        foreach ($key as $k) {
            $this->where[$k] = $cb;
        }
        return $this;
    }

    public function joinRule(ChangeJoin $join): self
    {
        $this->join[$join->tableObjClass] = $join;
        return $this;
    }

    /**
     * 字段 初始规则(必须包含所有涉及到的字段)
     *
     * @param                 $key
     * @param string|callable $cb
     * @return self
     */
    public function commonRule($key, $cb): self
    {
        $key = (array)$key;
        foreach ($key as $k) {
            $this->commonField[$k] = $this->handleFieldRule($cb, $k);
        }
        return $this;
    }

    /**
     * @param string          $key   替换前
     * @param string|callable $point 替换后
     * @return self
     */
    public function fieldRule(string $key, $point): self
    {
        return $this->fieldSceneRule($key, $point, 'field');
    }

    /**
     * @param string          $key   替换前
     * @param string|callable $point 替换后
     * @return self
     */
    public function fieldJoinRule(string $key, $point): self
    {
        return $this->fieldSceneRule($key, $point, 'fieldJoin');
    }

    /**
     * @param string          $key   替换前
     * @param string|callable $point 替换后
     * @return self
     */
    public function fieldWhereRule(string $key, $point): self
    {
        return $this->fieldSceneRule($key, $point, 'fieldWhere');
    }

    /**
     * @param string          $key   替换前
     * @param string|callable $point 替换后
     * @return self
     */
    public function fieldGroupRule(string $key, $point): self
    {
        return $this->fieldSceneRule($key, $point, 'fieldGroup');
    }

    /**
     * @param string          $key   替换前
     * @param string|callable $point 替换后
     * @return self
     */
    public function fieldOrderRule(string $key, $point): self
    {
        return $this->fieldSceneRule($key, $point, 'fieldOrder');
    }

    /**
     * @param string          $key   替换前
     * @param string|callable $point 替换后
     * @return self
     */
    public function fieldHavingRule(string $key, $point): self
    {
        return $this->fieldSceneRule($key, $point, 'fieldHaving');
    }

    /**
     * @param string $key
     * @param        $point
     * @param        $scene
     * @return $this
     */
    public function fieldSceneRule(string $key, $point, $scene): self
    {
        $this->{$scene}[$key] = $this->handleFieldRule($point, $key);
        return $this;
    }
}