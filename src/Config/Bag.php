<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/17/16
 * Time: 8:05 PM
 */

namespace Spw\Config;


use RuntimeException;

class Bag
{
    private static $configPath = WEB_ROOT . '/src/Config';

    public static function setConfigPath($path)
    {
        self::$configPath = $path;
    }
    /**
     * Read config depending on env setting.
     *
     * @return mixed
     * @throws RuntimeException
     */
    public static function get()
    {
        $mysqlConfigs = require self::$configPath . '/mysql.php';
        if (empty($mysqlConfigs)) {
            return [];
        }

        $env = Env::env();

        if (array_key_exists($env, $mysqlConfigs) && $mysqlConfigs[$env]) {
            return $mysqlConfigs[$env];
        }

        throw new RuntimeException("Environment ($env): not configured");
    }
}