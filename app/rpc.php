<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace rpc\contract\user;

use think\File as thinkFile;

interface User
{
	public function get($name);
}

interface Pay
{
	public function run($money);
}

interface File
{
	public function upload(thinkFile $file);
}
return ['user' => ['rpc\contract\user\User', 'rpc\contract\user\Pay', 'rpc\contract\user\File']];