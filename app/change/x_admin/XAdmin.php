<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/11 0011
// +----------------------------------------------------------------------
namespace app\change\x_admin;

use app\change\x_admin\tables\Login;
use app\change\x_admin\tables\User;

/**
 * Class XAdmin
 *
 * @property User $user
 * @property Login $login
 */
class XAdmin
{
    protected $namespace = __NAMESPACE__;

    public function __get($name)
    {
        $name = ucfirst($name);
        return app()->make($this->namespace . "\\tables\\{$name}", ['x_admin']);
    }
}