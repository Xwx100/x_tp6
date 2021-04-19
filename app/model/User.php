<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/9 0009
// +----------------------------------------------------------------------

namespace app\model;



class User extends \x_tp6\bases\Model
{

    protected $changeTableClass = \app\change\x_admin\tables\User::class;
}