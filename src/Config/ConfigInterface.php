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
    public function getRMDBSName(): string;

    /**
     * @return string
     */
    public function getHost(): string;

    /**
     * @return int
     */
    public function getPort(): int;

    /**
     * Database name.
     *
     * @return string
     */
    public function getDatabaseName(): string;

    /**
     * Character set.
     *
     * @return string
     */
    public function getDefaultCharset(): string;

    /**
     * User name.
     *
     * @return string
     */
    public function getUserName(): string;

    /**
     * Database password.
     *
     * @return string
     */
    public function getPassword(): string;
}