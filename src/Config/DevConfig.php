<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/9
 * Time: 0:12
 */

namespace Spw\Config;


class DevConfig extends AbstractConfig
{
    public function __construct($dbName)
    {
        $this->config = parse_ini_file('database.ini', true)[$dbName];
    }
}