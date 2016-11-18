<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/17/16
 * Time: 7:32 PM
 */

namespace Spw\Config;

use InvalidArgumentException;
use RuntimeException;


/**
 * Runtime environment settings.
 *
 * @package Spw\Config
 */
class Env
{
    const DEV = 'dev';
    const PROD = 'prod';

    const ENV_FILE = 'env';

    private static $env;

    private static $envList = [
        self::DEV,
        self::PROD,
    ];

    private static $alreadyInit = false;
    private static $cli;


    /**
     * Initialize environment setting if not set yet.
     *
     * @param null $env
     * @param bool $force
     * @param bool $cli
     * @return bool
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public static function init($env = null, $force = false, $cli = false)
    {
        if (self::$alreadyInit && $force !== true) {
            return true;
        }

        self::$cli = $cli;

        if (empty($env)) {
            $envFile = ROOT_DIR . DIRECTORY_SEPARATOR . self::ENV_FILE;
            if (!file_exists($envFile)) {
                throw new RuntimeException('Environment file (' . self::ENV_FILE . ') must be provided');
            }
            $env = trim(file_get_contents($envFile));
        }
        if (!in_array($env, self::$envList, true)) {
            throw new RuntimeException('Unexpected environment \'' . $env . '\'');
        }

        self::withEnv($env);
        self::$alreadyInit = true;

        return true;
    }


    /**
     * Get current environment setting.
     *
     * @return string
     */
    public static function env()
    {
        return self::$env;
    }

    /**
     * Check current environment is dev or not.
     *
     * @return bool
     */
    public static function dev()
    {
        return self::$env === self::DEV;
    }

    /**
     * Check current environment is product or not.
     *
     * @return bool
     */
    public static function prod()
    {
        return self::$env === self::PROD;
    }

    /**
     * Set runtime environment temporarily.
     *
     * @param $env
     * @throws InvalidArgumentException
     */
    public static function withEnv($env)
    {
        if (in_array($env, self::$envList, true)) {
            self::$env = $env;
        } else {
            throw  new InvalidArgumentException($env . ' environment does not exist');
        }
    }
}