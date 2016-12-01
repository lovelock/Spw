<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/29/16
 * Time: 11:26 PM
 */

namespace Spw;


interface ConnectionInterface
{
    public function from($table);

    public function into($table);

    /**
     * @param array|string $columns
     * @return mixed
     */
    public function select($columns = '*');

    public function where($wheres = []);

    /**
     * @param $column
     * @param string $asc
     * @return string
     */
    public function orderBy($column, $asc = 'desc');

    public function limit($limit);
    /**
     * @param array|string $columns
     * @return mixed
     */
    public function selectOne($columns = '*');

    /**
     * @param array $values
     * @return mixed
     * @throws \PDOException
     */
    public function insert(array $values);

    /**
     * @param array $values
     * @return mixed
     * @throws \PDOException
     */
    public function update(array $values);

    /**
     * @param array $bindings
     * @return mixed
     */
    public function prepareBinds(array $bindings);

    /**
     * @return string
     */
    public function getTable();

    /**
     * @return array|string
     */
    public function getColumns();

    /**
     * @return array
     */
    public function getOrderBy();

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @return array
     */
    public function getWheres();

    /**
     * @return array
     */
    public function getValues();

}