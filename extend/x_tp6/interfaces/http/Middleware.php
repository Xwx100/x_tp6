<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/8 0008
// +----------------------------------------------------------------------

namespace x_tp6\interfaces\http;


use think\Request;

interface Middleware
{
    public function handle(Request $request, $next);
}