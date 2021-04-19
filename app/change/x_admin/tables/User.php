<?php
// +----------------------------------------------------------------------
// | x: 2021-04-17 13:27:24
// +----------------------------------------------------------------------

namespace app\change\x_admin\tables;

/**
 * Class User
 * php think make:table mysql user --is_partition=1
 *
 * @property string $database
 * @property string $table
 * @property string $id
 * @property string $userId   用户id
 * @property string $userName 英文名
 * @property string $zh       中文名
 * @property string $phone    手机号
 * @property string $idCard   身份证
 * @property string $email    邮箱
 * @property string $createIp 创建IP
 * @property string $createAt 新增时间
 * @package app\change\x_admin\tables
 */
class User extends \x_tp6\services\change\Table
{
    public $connection = 'mysql';

    public $database = 'x_admin';

    public $table = 'user';

    public $isSubTable = false;

    public $createSql = "";

    public $isPartition = true;

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
        'user_id' =>
            array(
                'name' => 'user_id',
                'type' => 'varchar(30)',
                'notnull' => true,
                'default' => '',
                'primary' => false,
                'autoinc' => false,
                'comment' => '用户id',
                'type_only' => 'string',
            ),
        'user_name' =>
            array(
                'name' => 'user_name',
                'type' => 'varchar(15)',
                'notnull' => true,
                'default' => '',
                'primary' => false,
                'autoinc' => false,
                'comment' => '英文名',
                'type_only' => 'string',
            ),
        'zh' =>
            array(
                'name' => 'zh',
                'type' => 'varchar(15)',
                'notnull' => true,
                'default' => '',
                'primary' => false,
                'autoinc' => false,
                'comment' => '中文名',
                'type_only' => 'string',
            ),
        'phone' =>
            array(
                'name' => 'phone',
                'type' => 'varchar(20)',
                'notnull' => true,
                'default' => '',
                'primary' => false,
                'autoinc' => false,
                'comment' => '手机号',
                'type_only' => 'string',
            ),
        'id_card' =>
            array(
                'name' => 'id_card',
                'type' => 'varchar(30)',
                'notnull' => true,
                'default' => '',
                'primary' => false,
                'autoinc' => false,
                'comment' => '身份证',
                'type_only' => 'string',
            ),
        'email' =>
            array(
                'name' => 'email',
                'type' => 'varchar(256)',
                'notnull' => true,
                'default' => '',
                'primary' => false,
                'autoinc' => false,
                'comment' => '邮箱',
                'type_only' => 'string',
            ),
        'create_ip' =>
            array(
                'name' => 'create_ip',
                'type' => 'varchar(50)',
                'notnull' => true,
                'default' => '',
                'primary' => false,
                'autoinc' => false,
                'comment' => '创建IP',
                'type_only' => 'string',
            ),
        'create_at' =>
            array(
                'name' => 'create_at',
                'type' => 'timestamp',
                'notnull' => true,
                'default' => 'CURRENT_TIMESTAMP',
                'primary' => true,
                'autoinc' => false,
                'comment' => '新增时间',
                'type_only' => 'datetime',
            ),
    );

    protected $modelSchema = array(
        'id' => 'integer',
        'user_id' => 'string',
        'user_name' => 'string',
        'zh' => 'string',
        'phone' => 'string',
        'id_card' => 'string',
        'email' => 'string',
        'create_ip' => 'string',
        'create_at' => 'datetime',
    );

    protected $modelType = array(
        'create_at' => 'datetime',
    );
}