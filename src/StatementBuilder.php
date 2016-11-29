<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/29/16
 * Time: 11:54 PM
 */

namespace Spw;


class StatementBuilder
{
    public static function buildSelectStatement(ConnectionInterface $connection)
    {
        $columns = $connection->getColumns();
        if (is_array($columns)) {
            $implodedColumns = implode(', ', $connection->getColumns());
        } else {
            $implodedColumns = $columns;
        }
        return 'SELECT ' . $implodedColumns . ' FROM ' . $connection->getTable();
    }
}