<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/17/16
 * Time: 8:05 PM
 */

namespace Spw\Config;


use RuntimeException;

class Base
{
    /**
     * Read config depending on env setting.
     *
     * @return mixed
     * @throws RuntimeException
     */
    public static function get()
    {
        $env = Env::env();

        if (array_key_exists($env, static::$configs) && static::$configs[$env]) {
            return static::$configs[$env];
        }

        throw new RuntimeException("Environment ($env): not configured");
    }
}