<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/16 0016
// +----------------------------------------------------------------------

namespace x_tp6\bases;


use think\db\Query;

/**
 * 1. 增加分表功能：哈希后缀分表、单值分表、混合分表策略
 * 2. 智能 增加 create_by update_by
 * 3. 智能 增加 create_at update_at
 * 4. 智能 增加 connection、table、schema、type
 * Class Model
 *
 * @package x_tp6\providers
 */
class Model extends \think\Model
{
    protected $changeTableClass = null;
    /**
     * @var \x_tp6\services\change\interfaces\Table
     */
    public $changeTableObj = null;

    /**
     * 有值代表开启
     *
     * @var array
     */
    public $subRuleKeys = [];
    public $subRuleName = 'columns';// columns-字段分表
    public $subRuleOne = [];

    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';

    public static function injectChangeTable(): \Closure
    {
        return function (Model $m) {
            if (!class_exists($m->changeTableClass)) {
                if ($m->changeTableObj->isSubTable) {
                    x_app()->common->exception(get_called_class() . '该表是分表 未设置changeTable');
                }
                return;
            }
            empty($m->changeTableObj) && $m->changeTableObj = app($m->changeTableClass);
            $m->connection = $m->changeTableObj->connection;
            $m->table = $m->changeTableObj->table;
            $m->schema = $m->changeTableObj->modelSchema;
            $m->type = $m->changeTableObj->modelType;
            $m->subRuleKeys = $m->changeTableObj->subTableKeys;

            // 开启自动时间戳
            if (!empty($m->schema[$m->createTime]) || !empty($m->schema[$m->updateTime])) {
                $m->autoWriteTimestamp = true;
            }
        };
    }

    public static function onBeforeUpdate(Model $model)
    {
        self::updateBeforeUpdateBy($model);
        self::makeSubTable($model, $model->getWhere());
    }

    public static function onBeforeInsert(Model $model)
    {
        self::insertBeforeCreateBy($model);
        self::makeSubTable($model, $model->getWhere());
        if (self::isSubTableColumns($model)) {
            self::insertBeforeCheckTable($model);
        }
    }

    public static function onBeforeSelect(Query $query)
    {
        return self::onBeforeQuery($query);
    }

    public static function onBeforeFind(Query $query)
    {
        return self::onBeforeQuery($query);
    }

    public static function onBeforeQuery(Query $query)
    {
        $model = $query->getModel();
        if (self::isSubTableColumns($model)) {
            // 分表查询 只支持简单条件查询
            $where = (array)$query->getOptions('where');
            if ($where && !empty($where['AND'])) {
                $values = array_values($where['AND']);
                $whereAnd = array_combine(array_column($values, 0), array_column($values, 2));
                self::makeSubTable($model, $whereAnd);
            }
            $query->table($model->getTable());
        }
        return false;
    }

    /**
     * 判断 是否是 分表模式columns
     * @param self $model
     * @return bool
     */
    public static function isSubTableColumns($model): bool
    {
        return self::isSubTable($model) && $model->subRuleName === 'columns';
    }

    /**
     * 判断 是否是 分表
     * @param $model
     * @return bool
     */
    public static function isSubTable($model): bool
    {
        $is = is_subclass_of($model, self::class);
        $is = $is && $model->changeTableObj && $model->changeTableObj->isSubTable;
        return $is;
    }

    /**
     * 获取 分表后缀
     *
     * @param Model $model
     * @param array $subData
     * @return null
     * @throws \Exception
     */
    public static function makeSubTable(Model $model, array $subData)
    {
        // 自己定义 规则 命名方式如 subTable<subRuleName>
        if (self::isSubTable($model)) {
            $subNameStudly = x_app()->common->str()->studly($model->subRuleName);
            call_user_func_array(static::class . '::subTable' . $subNameStudly, [$model, $subData]);
        }
        return null;
    }

    /**
     * 分表规则-columns: 后缀哈希分表策略
     * [column, column]
     * @param Model $model
     * @param array $subData
     * @throws \Exception
     */
    public static function subTableColumns(Model $model, array $subData)
    {
        if (empty($model->subRuleKeys)) {
            x_app()->common->exception("分表规则:{$model->subRuleName} 还未设置keys");
        }
        $str = implode('_', x_app()->common->arr()->only($subData, $model->subRuleKeys));
        if (empty($str)) {
            x_app()->common->exception("分表规则:{$model->subRuleName} 数据维度错误");
        }
        $suffix = x_app()->common->subTable($str);
        $model->setSuffix($suffix);
    }

    /**
     * 分表规则-one: 一值分表策略
     * ['key' => 'origin', 'map' => ['houtai1' => '', 'houtai2' => '', 'houtai3' => '']]
     * @param Model $model
     * @param array $subData
     */
    public static function subTableOne(Model $model, array $subData) {
        if (empty($model->subRuleOne)) {
            x_app()->common->exception("分表规则:{$model->subRuleName} 还未设置keys");
        }
        $key = $model->subRuleOne['key'];
        $table = $model->subRuleOne[$subData[$key]];
        if (empty($table)) {
            x_app()->common->exception("分表规则:{$model->subRuleName} 数据维度错误");
        }
        $model->table = $table;


    }

    /**
     * 混合模式 1
     * ['sort' => ['columns', 'one'], 'mixed_map' => 'houtai1']
     * @param Model $model
     * @param array $subData
     */
    public static function subTableMixed(Model $model, array $subData) {
        if (empty($model->subRuleMixed)) {
            x_app()->common->exception("分表规则:{$model->subRuleName} 还未设置keys");
        }
        self::subTableOne($model, $subData);
        $mixedMap = x_app()->common->arr()->get($model->subRuleMixed, 'mixed_map');
        if ($mixedMap === $model->table) {
            self::subTableColumns($model, $subData);
        }
    }

    /**
     * 新增数据前 检查是否增加创建用户
     *
     * @param Model $model
     */
    public static function updateBeforeUpdateBy(Model $model)
    {
        if (empty($model->getAttr('update_by')) && !empty($model->schema['update_by']) && in_array('update_by', $model->userKeys())) {
            $model->set('update_by', '');
        }
    }

    /**
     * 新增数据前 检查是否增加创建用户
     *
     * @param Model $model
     */
    public static function insertBeforeCreateBy(Model $model)
    {
        if (empty($model->getAttr('create_by')) && !empty($model->schema['create_by']) && in_array('create_by', $model->userKeys())) {
            $model->set('create_by', '');
        }
    }

    /**
     * 新增数据前 检查分表是否存在
     *
     * @param Model $model
     * @throws \think\db\exception\BindParamException
     */
    public static function insertBeforeCheckTable($model)
    {
        if (self::isSubTableColumns($model) && $model->suffix) {
            /**
             * @var $c \think\db\PDOConnection
             */
            $c = $model->master(true)->getConnection();
            $sql = $model->changeTableObj->buildCreateSql($model->getTable());
            x_app()->common->log("创建sql: {$sql}");
            $c->execute($model->changeTableObj->buildCreateSql($model->getTable()));
        }
    }

    public function userKeys(): array
    {
        return ['create_by', 'update_by'];
    }
}