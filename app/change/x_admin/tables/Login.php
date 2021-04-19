<?php
// +----------------------------------------------------------------------
// | x: 2021-04-18 12:21:06
// +----------------------------------------------------------------------

namespace app\change\x_admin\tables;

/**
 * Class Login
 * php think make:table mysql login --sub_table_keys=flag_one,flag_two,type
 *
 * @property string $database
 * @property string $table
 * @property string $id
 * @property string $flagOne 登录标识1:type=1是账号名,type=2是手机号,type=3是邮箱,type=4是三方登录唯一标识
 * @property string $flagTwo 登录标识2:type=4是渠道
 * @property string $type    登录类型:1-普通,2-手机号,3-邮箱,4-三方登录
 * @package app\change\x_admin\tables
 */
class Login extends \x_tp6\services\change\Table
{
    public $connection = 'mysql';

    public $database = 'x_admin';

    public $table = 'login';

    public $isSubTable = true;

    public $createSql = "CREATE TABLE `login` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `flag_one` varchar(50) NOT NULL DEFAULT '' COMMENT '登录标识1:type=1是账号名,type=2是手机号,type=3是邮箱,type=4是三方登录唯一标识',
  `flag_two` varchar(50) NOT NULL DEFAULT '' COMMENT '登录标识2:type=4是渠道',
  `type` tinyint NOT NULL DEFAULT '1' COMMENT '登录类型:1-普通,2-手机号,3-邮箱,4-三方登录',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_type_flag` (`type`,`flag_one`,`flag_two`),
  KEY `idx_id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='登录'";

    public $subTableKeys = array(
        0 => 'flag_one',
        1 => 'flag_two',
        2 => 'type',
    );

    public $isPartition = false;

    protected $schema = array(
        'id' =>
            array(
                'name' => 'id',
                'type' => 'bigint',
                'notnull' => true,
                'default' => NULL,
                'primary' => true,
                'autoinc' => true,
                'comment' => '',
                'type_only' => 'integer',
            ),
        'flag_one' =>
            array(
                'name' => 'flag_one',
                'type' => 'varchar(50)',
                'notnull' => true,
                'default' => '',
                'primary' => false,
                'autoinc' => false,
                'comment' => '登录标识1:type=1是账号名,type=2是手机号,type=3是邮箱,type=4是三方登录唯一标识',
                'type_only' => 'string',
            ),
        'flag_two' =>
            array(
                'name' => 'flag_two',
                'type' => 'varchar(50)',
                'notnull' => true,
                'default' => '',
                'primary' => false,
                'autoinc' => false,
                'comment' => '登录标识2:type=4是渠道',
                'type_only' => 'string',
            ),
        'type' =>
            array(
                'name' => 'type',
                'type' => 'tinyint',
                'notnull' => true,
                'default' => '1',
                'primary' => false,
                'autoinc' => false,
                'comment' => '登录类型:1-普通,2-手机号,3-邮箱,4-三方登录',
                'type_only' => 'tinyint',
            ),
    );

    public $modelSchema = array(
        'id' => 'integer',
        'flag_one' => 'string',
        'flag_two' => 'string',
        'type' => 'tinyint',
    );

    public $modelType = array();
}