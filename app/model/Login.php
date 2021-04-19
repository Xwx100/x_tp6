<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/16 0016
// +----------------------------------------------------------------------

namespace app\model;


/**
 * Class Login
 * @property \app\change\x_admin\tables\Login $changeTableObj
 * @package app\model
 */
class Login extends \x_tp6\bases\Model
{
    protected $changeTableClass = \app\change\x_admin\tables\Login::class;
}