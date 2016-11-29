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
    public function getRMDBSName(): string
    {
        return 'mysql';
    }

    /**
     * Database name.
     *
     * @return string
     */
    public function getDatabaseName(): string
    {
        return 'spw';
    }

    /**
     * Character set.
     *
     * @return string
     */
    public function getDefaultCharset(): string
    {
        return 'utf8mb4';
    }

    /**
     * User name.
     *
     * @return string
     */
    public function getUserName(): string
    {
        return 'spw';
    }

    /**
     * Database password.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return 'spw';
    }

    public function getHost(): string
    {
        return '127.0.0.1';
    }

    public function getPort(): int
    {
        return 3306;
    }
}