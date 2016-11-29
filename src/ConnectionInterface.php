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
    public function from(string $table);

    /**
     * @param array $columns
     * @return mixed
     * @throws \PDOException
     */
    public function select($columns = '*');

    /**
     * @param array $columns
     * @return mixed
     * @throws \PDOException
     */
    public function selectOne($columns = ['*']);

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

    public function getTable();

    public function getColumns();

}