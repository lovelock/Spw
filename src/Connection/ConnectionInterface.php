<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/29/16
 * Time: 11:26 PM
 */

namespace Spw\Connection;


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
     * Set limit of a SQL statement.
     *
     * @param $limit
     * @return ConnectionInterface
     */
    public function limit($limit);

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
     * Get column names and values of a inserting of updating SQL statement.
     *
     * @return array
     */
    public function getValues();

}