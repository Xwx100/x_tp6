<?php
declare (strict_types=1);

namespace x_tp6\services\change\cmd;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Db;

class MakeTable extends Command
{
    protected $name = 'make:table';

    protected function configure()
    {
        // 指令配置
        $this->setName($this->name)
            ->addArgument('connect_name', Argument::REQUIRED, "数据库连接配置")
            ->addArgument('tables', Argument::IS_ARRAY, '表名')
            ->addOption('output_dir', null, Option::VALUE_REQUIRED, "root输出目录", 'app/change/x_admin/tables')
            ->addOption('tpl_file', null, Option::VALUE_REQUIRED, "root输出目录", __DIR__ . '/Table.php.tpl')
            ->addOption('is_sub_table', null, Option::VALUE_REQUIRED, '是否是分表', 0)
            ->addOption('is_partition', null, Option::VALUE_REQUIRED, '是否是分区', 0)
            ->addOption('sub_table_keys', null, Option::VALUE_REQUIRED, '分表字段', '')
            ->setDescription('制作模型属性');
    }


    protected function execute(Input $input, Output $output)
    {

        try {
            $name = $input->getArgument('connect_name');
            $outputDir = $input->getOption('output_dir');
            $tplFile = $input->getOption('tpl_file');
            $tables = $input->getArgument('tables');
            $isSubTable = $input->getOption('is_sub_table');
            $isPartition = $input->getOption('is_partition');
            $subTableKeys = $input->getOption('sub_table_keys');
            if ($subTableKeys) {
                $subTableKeys = explode(',', $subTableKeys);
                $isSubTable = 1;
            }

            $c = Db::connect($name);
            if (empty($tables)) {
                $tables = $c->getTables();
            }
            $root = app()->getRootPath();
            $outputDirAll = $root . str_replace(['\\'], ['/'], trim(trim($outputDir), '\\/')) . DIRECTORY_SEPARATOR;
            if (count(scandir($outputDirAll)) > 2) {
                x_app()->common->log("输出目录：{$outputDirAll}-存在文件,请先清空");
            }
            $query = x_app()->thinkQuery([$c], false);
            $tplContent = file_get_contents($tplFile);
            $tplArr = [
                'headAuthor' => 'x',
                'headDate' => date('Y-m-d H:i:s'),
                'namespace' => str_replace(['/'], ['\\'], $outputDir),
                'database' => $c->getConfig('database'),
                'connection' => $name,
                'className' => null,
                'classProperty' => null,
                'classTable' => null,
                'classSchema' => null,
                'modelSchema' => null,
                'modelType' => null,
                'isSubTable' => null,
                'isPartition' => null,
                'createSql' => null,
                'recordCli' => null,
                'subTableKeys' => null,
            ];
            $search = array_map(function ($key) {
                return "<{$key}>";
            }, array_keys($tplArr));
            foreach ($tables as $table) {
                $fields = $query->table($table)->getFields();
                array_walk($fields, function (&$field, $k) {
                    $type = $field['type'];
                    list($type,) = explode('(', $type, 2);
                    $maps = [
                        'varchar' => 'string',
                        'float' => 'float',
                        'int' => 'integer',
                        'bigint' => 'integer',
                        'timestamp' => 'datetime',
                        'datetime' => 'datetime',
                    ];
                    $field['type_only'] = $maps[$type] ?? $type;
                });
                $tplArr['classTable'] = $table;
                $tplArr['className'] = x_app()->common->str()->studly($table);
                $tplArr['classSchema'] = $this->arrToString($fields);
                foreach ($fields as $field) {
                    $name = x_app()->common->str()->camel($field['name']);
                    $tplArr['classProperty'][] = " * @property string \${$name} {$field['comment']}";
                }
                $tplArr['classProperty'] = implode("\n", $tplArr['classProperty']);
                $tplArr['modelSchema'] = $this->arrToString($this->getModelSchema($fields));
                $tplArr['modelType'] = $this->arrToString($this->getModelType($fields));
                $tplArr['isSubTable'] = $isSubTable ? 'true' : 'false';
                $tplArr['isPartition'] = $isPartition ? 'true' : 'false';
                $tplArr['recordCli'] = 'php ' . implode(' ', $_SERVER['argv']);
                $tplArr['subTableKeys'] = $this->arrToString($subTableKeys);
                if ($tplArr['isSubTable']) {
                    $tplArr['createSql'] = $c->query("show create table {$tplArr['database']}.{$table}")[0]['Create Table'];
                }
                $replace = array_values($tplArr);
                $tplContentReal = str_replace($search, $replace, $tplContent);
                file_put_contents("{$outputDirAll}{$tplArr['className']}.php", $tplContentReal);
            }

            $output->writeln($this->name);
        } catch (\Exception $e) {
            x_app()->common->log($e);
        }

    }

    public function arrToString($arr): string
    {
        return var_export($arr, true);
    }

    public function getModelSchema($schema): array
    {
        return array_combine(
            array_keys($schema),
            array_column($schema, 'type_only')
        );
    }

    public function getModelType($schema): array
    {
        $arr = array_filter($schema, function ($filed) {
            return in_array($filed['type_only'], ['datetime']);
        });

        return array_combine(
            array_keys($arr),
            array_column($arr, 'type_only')
        );
    }
}
