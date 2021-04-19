<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/8 0008
// +----------------------------------------------------------------------

namespace x_tp6\services\change;

use think\db\Query;
use think\facade\Db;
use x_tp6\services\change\interfaces\Table;

/**
 * 面向对象编程，使其跟写原生sql一致
 * 智能判断分组，使其符合 only full group by 特性
 * 智能join，可多表连接自动退化成单表
 * 快速field
 * 快速where
 * 多表连接时，自动解决表前缀
 * Class Change
 *
 * @package x_tp6\services\change
 */
class Change
{
    /**
     * @var Name
     */
    public $name = null;
    public $front = null;
    public $back = null;

    /**
     * @var Table
     */
    public $base = null;
    public $baseClass = null;
    /**
     * @var bool
     */
    public $limit;
    public $total;

    use ChangeRule;

    /**
     * @var Query
     */
    public $query = null;

    public function __construct(Name $name)
    {
        $this->name = $name;
        $this->front = x_app()->changeItem();
        $this->back = x_app()->changeItem();
    }

    public function front(array $front): Change
    {
        $this->front->run($front);
        return $this;
    }

    /**
     * 是否计算总条数
     * @param $total
     * @return $this
     */
    public function total($total): Change
    {
        $this->total = $total;
        return $this;
    }

    public function base(Table $base): Change
    {
        $this->base = $base;
        $this->baseClass = $base->getClass();
        return $this;
    }

    public function run(): Change
    {
        $this->join()->field()->where()->group()->limit()->order()->having();
        return $this;
    }

    /**
     * 获取 模型query类
     *
     */
    public function query(): Change
    {
        if (is_a($this->query, Query::class)) {
            return $this;
        }

        $query = x_app()->thinkQuery([Db::connect($this->base->connection)], false);
        $query->table($this->base->table);

        foreach ($this->back->join as $join) {
            $query->join(...$join);
        }

        if ($this->back->page) {
            $query->limit(...$this->back->page);
        }

        $query
            ->field(array_values($this->back->field))
            ->group($this->back->group)
            ->order($this->back->order)
            ->where($this->back->where);

        $this->query = $query;
        return $this;
    }

    /**
     * 前端数据
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function lists()
    {
        if ($this->total) {
            $total = $this->query()->queryTotalCount();
        }
        $data = $this->query->select()->toArray();
        return [
            'data' => $data,
            'page' => $this->resPage($total ?? 0)
        ];
    }

    /**
     * 返回前端分页格式
     *
     * @param int $total
     * @return array
     */
    public function resPage(int $total): array
    {
        $page = $this->front->page;
        $page['total'] = $total;
        $page['total_page'] = ceil($total / $page[$this->name->pageSize]);
        return $page;
    }

    /**
     * @param Query $query
     * @return int
     */
    public function queryTotalCount(): int
    {
        $newQuery = clone $this->query;
        return $this->queryTotal($newQuery)->count();
    }

    /**
     * 去掉分页查询
     *
     * @param Query $query
     * @return Query
     */
    public function queryTotal(Query $query): Query
    {
        $query->setOption('limit', null);
        $query->setOption('order', []);
        return $query;
    }

    /**
     * 前提：需要配置除基表字段之外的所有字段
     *
     * @return $this
     */
    public function field(): Change
    {
        $fields = [];

        array_map(function ($field) use (&$fields) {
            $result = $this->getFieldRule($field);
            if ($result) {
                // 假如有group 则判断是否处在分组内或者聚合函数
                if (
                    empty($this->front->group)
                    || ($this->front->group
                        && (in_array($field, $this->front->group) || $this->fieldIsGroup($result)))
                ) {
                    $fields[$field] = $result;
                }
            }
        }, $this->front->field);

        $this->back->field = $fields;
        return $this;
    }

    /**
     * 查看涉及到哪些表
     *
     * @return $this
     */
    public function join(): Change
    {
        $classes = $this->front->join;
        if (empty($classes)) {
            return $this;
        }
        $classes = array_map(function ($v) {
            if (is_a($v, Table::class)) {
                return $v->getClass();
            }
            return $v;
        }, $classes);


        $joins = [];
        foreach ($classes as $class) {
            $rule = $this->getJoinRule($class);
            if (is_array($rule->trigger)) {
                $isJoin = array_intersect($rule->trigger, $this->front->field);
            } else {
                $isJoin = call_user_func($rule->trigger, $this->front->field);
            }
            if ($isJoin) {
                $fields = $this->getFieldJoinRule(null);
                $on = str_replace(array_keys($fields), array_values($fields), $rule->on);
                $joins[] = [$rule->getTable(), $on, $rule->type];
            }
        }

        $this->back->join = $joins;

        return $this;
    }

    public function where(): Change
    {
        $where = [];
        foreach ($this->front->where as $key => $value) {
            $whereFunc = $this->getWhereRule($key);
            if (empty($whereFunc)) {
                $this->whereRule($key, null);
                $whereFunc = $this->getWhereRule($key);
            }
            $value = call_user_func($whereFunc, $value, $this->getFieldWhereRule($key));
            if ($value) {
                $where[] = $value;
            }
        }

        $this->back->where = $where;
        return $this;
    }

    public function group(): Change
    {
        $groups = array_map(function ($group) {
            return $this->getFieldGroupRule($group);
        }, $this->front->group);

        $this->back->group = array_filter($groups);

        return $this;
    }

    public function order(): Change
    {
        $order = array_map(function ($order) {
            $sortField = $order[x_app()->change->name->sortField] ?? '';
            $sortType = $order[x_app()->change->name->sortType] ?? 'asc';
            if (empty($sortType) && empty($sortField)) {
                return '';
            }
            $f = $this->getFieldOrderRule($sortField);
            if ($f) {
                $f .= " {$sortType}";
            }
            return $f;
        }, $this->front->order);

        $this->back->order = array_filter($order);
        return $this;
    }

    public function having()
    {

    }

    public function limit(): Change
    {
        if (false === $this->limit) {
            return $this;
        }

        $page = $this->front->page[$this->name->page];
        $pageSize = $this->front->page[$this->name->pageSize];
        if ($page && $pageSize) {
            $this->back->page = [($page - 1) * $pageSize, $pageSize];
        }

        return $this;
    }

    /**
     * 不分页 强制
     *
     * @param bool $limit
     * @return $this
     */
    public function noLimit($limit = true): Change
    {
        $this->limit = false;
        $this->back->page = [];
        return $this;
    }

    public function checkLimit(): Change
    {
        if (false === $this->limit) {
            return $this;
        }

        $this->front->page[$this->name->page] = x_app()->common->arr()->get(
            $this->front->page,
            $this->name->page,
            1
        );
        $this->front->page[$this->name->pageSize] = x_app()->common->arr()->get(
            $this->front->page,
            $this->name->pageSize,
            200
        );
        if ($this->front->page[$this->name->page] > 100000) {
            x_app()->common->exception('超出最大页数100000');
        }
        if ($this->front->page[$this->name->pageSize] > 200) {
            x_app()->common->exception('超出每页最多数量200');
        }
        return $this;
    }

    public function filterWhere(): Change
    {
        $this->front->where = array_filter($this->front->where, function ($value) {
            return $value !== '';
        });
        return $this;
    }

    public function filterField()
    {
        // group 与 field 相等
        if ($this->group && $this->field) {

            $this->field = array_intersect($this->group, $this->field);
        }
    }

    /**
     * 判断 字符串 是否是 聚合函数
     *
     * @param string $str
     * @return bool
     */
    public function fieldIsGroup(string $str)
    {
        foreach ($this->isGroupRe() as $k) {
            return false !== stripos($str, $k);
        }
        return false;
    }

    public function isGroupRe()
    {
        return ['group_concat', 'sum', 'count'];
    }
}

/*
// 实例
x_app()
    ->change()
    ->commonRule(
        x_app()->xAdmin->user->getSchemaKeysExclude([x_app()->xAdmin->user->id]),
        function ($key) {
            return x_app()->xAdmin->user->table . ".{$key}";
        }
    )
    ->fieldJoinRule("{1}", "user1.{$xApp->xAdmin->user->userId}")
    ->fieldRule(x_app()->xAdmin->user->userName, function ($key) {
        return x_app()->xAdmin->user->groupConcat(x_app()->xAdmin->user->getTableField(x_app()->xAdmin->user->userName), $key);
    })
    ->joinRule(
        x_app()->changeJoin()
            ->leftJoin(x_app()->xAdmin->user)
            ->as('user1')
            ->on("{$xApp->xAdmin->user->userId}={1}")
            ->trigger(function (array $fields) {
                return array_intersect(x_app()->xAdmin->user->getSchemaKeysExclude(['id']), $fields);
            })
    )
    ->whereRule(x_app()->xAdmin->user->getSchemaKeysExclude([
        x_app()->xAdmin->user->userId,
        x_app()->xAdmin->user->userName,
        x_app()->xAdmin->user->createAt
    ]))
    ->whereRule(x_app()->xAdmin->user->userId, 'in')
    ->whereRule(x_app()->xAdmin->user->userName, 'left_like')
    ->whereRule(x_app()->xAdmin->user->createAt, 'datetime')
    ->base(x_app()->xAdmin->user)
    ->front([
        x_app()->change->name->field => x_app()->xAdmin->user->getFields(),
        x_app()->change->name->where => [
            x_app()->xAdmin->user->userId => [
                1, 2, 3, 4
            ]
        ],
        x_app()->change->name->group => [
            x_app()->xAdmin->user->userId,
        ],
        x_app()->change->name->order => [
            [
                x_app()->change->name->sortField => x_app()->xAdmin->user->userId
            ],
        ],
        x_app()->change->name->join => [
            x_app()->xAdmin->user
        ],
        x_app()->change->name->page => [
            x_app()->change->name->page => 10,
            x_app()->change->name->pageSize => 100,
        ]
    ])
    ->checkLimit()
    ->filterWhere()
    ->run()
    ->query()
    ->lists()
 */