<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/29/16
 * Time: 11:02 PM
 */

namespace Spw\Config;


class DevConfig implements ConfigInterface
{
    /**
     * Get RMDBS name: mysql for default.
     *
     * @return string
     */
    public function getRMDBSName()
    {
        return 'mysql';
    }

    /**
     * Database name.
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return 'spw';
    }

    /**
     * Character set.
     *
     * @return string
     */
    public function getDefaultCharset()
    {
        return 'utf8mb4';
    }

    /**
     * User name.
     *
     * @return string
     */
    public function getUserName()
    {
        return 'spw';
    }

    /**
     * Database password.
     *
     * @return string
     */
    public function getPassword()
    {
        return 'spw';
    }

    public function getHost()
    {
//        return '127.0.0.1';
        return '192.168.159.3';
    }

    public function getPort()
    {
        return 3306;
    }
}