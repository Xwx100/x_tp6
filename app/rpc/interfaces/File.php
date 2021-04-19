<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/8 0008
// +----------------------------------------------------------------------

namespace app\rpc\interfaces;


interface File
{
    public function upload(\think\File $file);
}