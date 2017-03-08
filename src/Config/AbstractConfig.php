<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/29/16
 * Time: 11:02 PM
 */

namespace Spw\Config;


abstract class AbstractConfig implements ConfigInterface
{
    protected $config;

    public abstract function __construct($dbName);

    /**
     * Get RMDBS name: mysql for default.
     *
     * @return string
     */
    public function getRMDBSName()
    {
        return $this->config['rmdbs'];
    }

    /**
     * Database name.
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->config['dbname'];
    }

    /**
     * Character set.
     *
     * @return string
     */
    public function getDefaultCharset()
    {
        return $this->config['charset'];
    }

    /**
     * User name.
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->config['user'];
    }

    /**
     * Database password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->config['password'];
    }

    public function getHost()
    {
        return $this->config['host'];
    }

    public function getPort()
    {
        return $this->config['port'];
    }
}