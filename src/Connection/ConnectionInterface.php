<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/29/16
 * Time: 11:26 PM
 */

namespace Spw\Connection;


use Psr\Log\LoggerInterface;

interface ConnectionInterface
{
    /**
     * Set table name to operate.
     *
     * @param $table
     * @return ConnectionInterface
     */
    public function from($table);

    /**
     * Alias of method from.
     *
     * @param $table
     * @return ConnectionInterface
     */
    public function into($table);

    /**
     * Execute query action.
     *
     * @param array|string $columns
     * @return mixed
     */
    public function select($columns = '*');

    /**
     * Count specified column.
     *
     * @param string $col
     * @param string $alias
     * @param bool $distinct
     * @return mixed
     */
    public function count($col, $alias, $distinct = false);

    /**
     * Construct where clause of a SQL statement.
     *
     * @param array $wheres
     * @return ConnectionInterface
     */
    public function where($wheres = []);

    /**
     * Construct order by clause of a SQL statement.
     *
     * @param $column
     * @param string $asc
     * @return ConnectionInterface
     */
    public function orderBy($column, $asc = 'desc');

    /**
     * Construct group by clause of a SQL statement.
     *
     * @param $column
     * @return ConnectionInterface
     */
    public function groupBy($column);

    /**
     * Set limit of a SQL statement.
     *
     * @param $offset
     * @param $count
     * @return ConnectionInterface
     */
    public function limit($offset, $count = 0);

    /**
     * Select single row of data set.
     *
     * @param array|string $columns
     * @return mixed
     */
    public function selectOne($columns = '*');

    /**
     * Execute insert action.
     *
     * @param array $values
     * @return mixed
     * @throws \PDOException
     */
    public function insert(array $values);

    /**
     * Execute replace action.
     *
     * @param array $values
     * @return mixed
     */
    public function replace(array $values);

    /**
     * Execute update action.
     *
     * @param array $values
     * @return mixed
     * @throws \PDOException
     */
    public function update(array $values);

    /**
     * Get table name.
     *
     * @return string
     */
    public function getTable();

    /**
     * Get columns of a select SQL statement.
     *
     * @return array|string
     */
    public function getColumns();

    /**
     * Get order by of a SQL statement.
     *
     * @return array
     */
    public function getOrderBy();

    /**
     * Get group by of a SQL statement.
     *
     * @return string
     */
    public function getGroupBy();

    /**
     * Get limit of a SQL statement.
     *
     * @return int
     */
    public function getLimit();

    /**
     * Get where clause of a SQL statement.
     *
     * @return array
     */
    public function getWheres();

    /**
     * Get column names and values of a inserting or updating SQL statement.
     *
     * @return array
     */
    public function getValues();

    /**
     * Get count clause of a select SQL statement.
     *
     * @return array
     */
    public function getCounts();

    /**
     * Get number of rows of specific condition.
     *
     * @return integer
     */
    public function getNumRows();


    /**
     * Run raw sql and return result.
     *
     * @param $sql
     * @param array $params
     * @return array
     */
    public function raw($sql, array $params);

    /**
     * Set logger to log queries.
     * @param LoggerInterface $logger
     * @return ConnectionInterface
     */
    public function setLogger(LoggerInterface $logger);

}