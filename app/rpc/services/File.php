<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/8 0008
// +----------------------------------------------------------------------

namespace app\rpc\services;


class File implements \app\rpc\interfaces\File
{
    public function upload(\think\File $file) {
        $file->move(app()->getRuntimePath(), 'test');
        return true;
    }
}