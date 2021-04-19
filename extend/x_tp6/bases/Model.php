<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/16 0016
// +----------------------------------------------------------------------

namespace x_tp6\bases;


use think\db\Query;

/**
 * 1. 增加分表功能
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
        self::makeSubTableSuffix($model, $model->getWhere());
    }

    public static function onBeforeInsert(Model $model)
    {
        self::insertBeforeCreateBy($model);
        self::makeSubTableSuffix($model, $model->getData());
        if ($model->getSuffix()) {
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
        if (is_subclass_of($model, self::class) && $model->changeTableObj && $model->changeTableObj->isSubTable) {
            // 分表查询 只支持简单条件查询
            $where = (array)$query->getOptions('where');
            if ($where && !empty($where['AND'])) {
                $values = array_values($where['AND']);
                $whereAnd = array_combine(array_column($values, 0), array_column($values, 2));
                self::makeSubTableSuffix($model, $whereAnd);
            }
            $query->table($model->getTable());
        }
        return false;
    }

    /**
     * 获取 分表后缀
     *
     * @param Model $model
     * @param array $subData
     * @return string
     * @throws \Exception
     */
    public static function makeSubTableSuffix(Model $model, array $subData)
    {
        if (!is_object($model->changeTableObj) || !$model->changeTableObj->isSubTable) {
            return '';
        }
        if (empty($model->getSuffix())) {
            // 自己定义 规则 命名方式如 subTable<subRuleName>
            $subNameStudly = x_app()->common->str()->studly($model->subRuleName);
            call_user_func_array(static::class . '::subTable' . $subNameStudly, [$model, $subData]);
        }
        return $model->getSuffix();
    }

    /**
     * 分表规则-columns
     *
     * @param Model $model
     * @param array $subData
     * @throws \Exception
     */
    public static function subTableColumns(Model $model, array $subData)
    {
        // 默认规则 columns
        if ($model->subRuleName === 'columns') {
            if (empty($model->subRuleKeys)) {
                x_app()->common->exception("分表规则:{$model->subRuleName} 还未设置keys");
            }
            $str = implode('_', x_app()->common->arr()->only($subData, $model->subRuleKeys));
            if (empty($str)) {
                x_app()->common->exception("分表规则:{$model->subRuleName} 数据维度错误");
            }
            $suffix = x_app()->common->subTable($str);
            $model->setSuffix($suffix);
        } else {
            x_app()->common->exception("分表规则:{$model->subRuleName} 还未封装");
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
    public static function insertBeforeCheckTable(Model $model)
    {
        if ($model->suffix) {
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