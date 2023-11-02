<?php
/**
 * 逻辑层基类
 * 公共的逻辑方法
 */

namespace Layer\Base\Logics;

use Layer\Base\Traits\UtilTrait;
use Redis;

class BaseLogic
{
    use UtilTrait;

    private static ?self $instance = null;

    /**
     * 实例化
     * @param ...$arguments
     * @return static
     */
    public static function instance(...$arguments): static
    {
        if (!self::$instance) {
            self::$instance = new static(...$arguments);
        }
        return self::$instance;
    }

    /**
     * @param string $name
     * @return Redis
     */
    protected function redis(string $name = 'default'): Redis
    {
        return app('redis')->connection($name);
    }
}
