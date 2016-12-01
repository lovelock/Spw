<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/29/16
 * Time: 10:51 PM
 */

namespace Spw\Config;


interface ConfigInterface
{
    /**
     * Get RMDBS name: mysql for default.
     *
     * @return string
     */
    public function getRMDBSName();

    /**
     * @return string
     */
    public function getHost();

    /**
     * @return int
     */
    public function getPort();

    /**
     * Database name.
     *
     * @return string
     */
    public function getDatabaseName();

    /**
     * Character set.
     *
     * @return string
     */
    public function getDefaultCharset();

    /**
     * User name.
     *
     * @return string
     */
    public function getUserName();

    /**
     * Database password.
     *
     * @return string
     */
    public function getPassword();
}