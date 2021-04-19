<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/8 0008
// +----------------------------------------------------------------------

namespace x_tp6\services\change;


use think\helper\Str;

/**
 * Class Name
 *
 * @property string $field
 * @property string $join
 * @property string $where
 * @property string $group
 * @property string $order
 * @property string $having
 * @property string $page      当前页
 * @property string $pageSize  每页多少
 * @property string $pageLimit 是否分页
 * @property string $sortField 排序字段
 * @property string $sortType  排序类型
 *
 * @property string $triggerKey 触发键
 * @property string $sqlTable 表名
 * @property string $sqlKey sql原始字段
 * @property string $sqlAlias sql重命名字段
 * @property string $sqlFormat sql格式化字符串或者函数
 * @package x_tp6\services\change
 */
class Name
{
    public function __get($name)
    {
        return Str::snake($name);
    }
}