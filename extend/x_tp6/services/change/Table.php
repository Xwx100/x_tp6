<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/14 0014
// +----------------------------------------------------------------------

namespace x_tp6\services\change;

/**
 * Class Table
 *
 * @package x_tp6\services\change
 */
abstract class Table implements \x_tp6\services\change\interfaces\Table
{
    protected $schema = [];


    public function getSchemaKeysExclude($exclude = []) {
        return array_diff($this->getSchemaKeys(), $exclude);
    }

    public function getSchemaKeysInclude($include = []) {
        return x_app()->common->arr()->only($this->getSchemaKeys(), $include);
    }

    public function getSchemaKeys() {
        if (empty($this->schemaKeys)) {
            $this->schemaKeys = array_keys($this->schema);
        }
        return $this->schemaKeys;
    }

    public function getTableField($field): string
    {
        return "{$this->table}.{$field}";
    }

    public function getDbTableField($field): string
    {
        return "{$this->database}.{$this->table}.{$field}";
    }

    public function getFieldType($field): string
    {
        return $this->get('schema', "{$field}.type");
    }

    public function getFieldComment($field): string
    {
        return $this->get('schema', "{$field}.comment");
    }

    public function getFields(): array
    {
        return array_keys($this->schema);
    }

    public function get(string $prop, string $key = null)
    {
        $value = (array)$this->$prop;
        return x_app()->common->arr()->get($value, $key);
    }

    public function getClass(): string
    {
        if (empty($this->class)) {
            $this->class = get_called_class();
        }

        return $this->class;
    }

    public function getShortClass(): string
    {
        if (empty($this->shortClass)) {
            $this->shortClass = class_basename($this->getClass());
        }

        return $this->shortClass;
    }

    public function __get($name): string
    {
        return x_app()->common->str()->snake($name);
    }

    public function sum($sum, $as): string
    {
        return "sum($sum)" . $this->as($as);
    }

    public function groupConcat($groupConcat, $as = null): string
    {
        return "group_concat({$groupConcat})" . $this->as($as);
    }

    public function count($count, $as = null): string
    {
        return "count({$count})" . $this->as($as);
    }

    public function caseWhen($caseWhen, $then, $else, $as = null): string
    {
        return "case when ({$caseWhen}) then {$then} else {$else} end" . $this->as($as);
    }

    public function as($as = null): string
    {
        return ($as ? " as {$as}" : "");
    }

    public function buildCreateSql($newTable): string
    {
        $createSql = $this->createSql;
        if (empty($createSql)) {
            return $createSql;
        }
        return preg_replace('/CREATE TABLE `(.*?)`/', "CREATE TABLE if NOT EXISTS `{$newTable}`", $createSql);
    }
}