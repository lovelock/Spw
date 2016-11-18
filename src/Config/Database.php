<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/17/16
 * Time: 7:32 PM
 */

namespace Spw\Config;


/**
 * Database related config.
 *
 * @package Spw\Config
 */
class Database extends Base
{
    protected static $configs = [
        Env::DEV => [
            'testpdo' => [
                'write' => ['192.168.159.3'],
                'port' => '3306',
                'database' => 'testpdo',
                'username' => 'kop',
                'password' => 'test',
                'charset' => 'utf8mb4',
            ],
        ],
        Env::PROD => [
        ],
    ];

}