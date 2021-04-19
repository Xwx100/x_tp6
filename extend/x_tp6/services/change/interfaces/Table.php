<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/14 0014
// +----------------------------------------------------------------------

namespace x_tp6\services\change\interfaces;


/**
 * Interface Table
 *
 * @property string $connection  连接标识
 * @property string $database    库名
 * @property string $table       表名
 * @property string $class       当前类名
 * @property string $shortClass  当前短类名
 * @property array  $modelSchema tp模型字段
 * @property array  $modelType   tp模型自动抓换字段
 * @property bool   $isSubTable  tp模型是否分表
 * @property string $createSql   tp模型分表时创建表语句
 * @property bool   $isPartition tp模型是否分区
 * @property array  $subTableKeys tp模型分表采用字段
 * @package x_tp6\services\change\interfaces
 */
interface Table
{
    // 获取 带表名字段
    public function getTableField($field): string;

    // 获取 带库表名字段
    public function getDbTableField($field): string;

    // 获取 字段类型
    public function getFieldType($field): string;

    // 获取 字段注释
    public function getFieldComment($field): string;

    // 获取 实例属性
    public function get(string $prop);

    // 获取 当前类
    public function getClass(): string;

    // 获取 所有字段
    public function getFields(): array;

    // 生成 创建sql语句
    public function buildCreateSql($newTable): string;
}