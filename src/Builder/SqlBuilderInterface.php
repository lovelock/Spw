<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/30/16
 * Time: 5:38 PM
 */

namespace Spw\Builder;


use Spw\Connection\ConnectionInterface;

interface SqlBuilderInterface
{
    /**
     * @param ConnectionInterface $connection
     * @return mixed
     */
    public static function buildSelectSql(ConnectionInterface $connection);

    /**
     * @param ConnectionInterface $connection
     * @return mixed
     */
    public static function buildInsertSql(ConnectionInterface $connection);

    /**
     * @param ConnectionInterface $connection
     * @return mixed
     */
    public static function buildUpdateSql(ConnectionInterface $connection);

    /**
     * @param ConnectionInterface $connection
     * @return mixed
     */
    public static function buildDeleteSql(ConnectionInterface $connection);
}