<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/6 0006
// +----------------------------------------------------------------------

namespace x_tp6\services\lang;


use think\App;
use think\helper\Arr;
use think\Manager;
use x_tp6\services\lang\driver\Baidu;
use x_tp6\services\lang\driver\Base;
use x_tp6\services\lang\driver\Custom;

/**
 * 语言包 (自定义 => api)
 * Class Lang
 * @mixin Base
 * @package x_tp6\lang
 */
class Lang extends Manager
{
    protected $custom = null;
    protected $namespace = __NAMESPACE__ . '\\driver\\';

    public function __construct(App $app, Custom $custom)
    {
        $this->custom = $custom;
        parent::__construct($app);
    }


    public function run($en)
    {
        // 自定义 api
        $dispatches = [
            x_app()->common->closure($this->custom),
            x_app()->common->closure($this->driver())
        ];

        foreach ($dispatches as $dispatch) {
            /**
             * @var Custom|Baidu $obj
             */
            $obj = $dispatch();
            $value = $obj->run($en);
            if ($value) {
                break;
            }
        }

        return $value;
    }

    /**
     * 获取默认驱动
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->getConfig('default');
    }

    /**
     * 获取总配置
     *
     * @access public
     * @param null|string $name    名称
     * @param mixed       $default 默认值
     * @return mixed
     */
    public function getConfig(string $name = null, $default = null)
    {
        if (!is_null($name)) {
            return $this->app->config->get('x_tp6.lang.' . $name, $default);
        }
        return $this->app->config->get('x_tp6');
    }

    /**
     * 获取驱动配置
     *
     * @param string $drive
     * @param string $name
     * @param null   $default
     * @return array
     */
    public function getDriveConfig(string $drive, string $name = null, $default = null)
    {
        if ($config = $this->getConfig("drives.{$drive}")) {
            return Arr::get($config, $name, $default);
        }

        throw new \InvalidArgumentException("drive [$drive] not found.");
    }

    /**
     * 获取驱动类
     *
     * @param string $name
     * @return array|mixed|string
     */
    protected function resolveType(string $name)
    {
        return $this->getDriveConfig($name, 'type', $this->getConfig('default'));
    }

    /**
     * 向获取驱动配置
     *
     * @param string $name
     * @return array|mixed|string
     */
    protected function resolveConfig(string $name)
    {
        return $this->getDriveConfig($name);
    }
}