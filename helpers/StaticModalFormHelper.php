<?php

namespace fishvision\modalforms\helpers;

/**
 * Class StaticModalFormHelper
 * @package fishvision\modalforms\helpers
 */
class StaticModalFormHelper
{
    /**
     * @var array
     */
    private static $registered = [];

    /**
     * @param $name
     * @return bool
     */
    public static function isRegistered($name)
    {
        return isset(static::$registered[$name]);
    }

    /**
     * @param $name
     */
    public static function register($name)
    {
        static::$registered[$name] = true;
    }

    /**
     * @return int
     */
    public static function registeredCount()
    {
        return sizeof(static::$registered);
    }
}