<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/16/16
 * Time: 4:47 PM
 */

namespace Spw;

use InvalidArgumentException;
use RuntimeException;
use Spw\Config\Bag;
use Spw\Config\Database;
use Spw\Support\Str;
use Spw\Utils\Timer;

class Model
{
    /**
     * The database the query is targeting
     *
     * @var string
     */
    protected $database;

    /**
     * The table which the query is targeting
     *
     * @var string
     */
    protected $table;

    /**
     * The columns that should be returned.
     *
     * @var array|string
     */
    protected $columns;

    /**
     * The where constraints for the query.
     *
     * @var array
     */
    protected $wheres;

    /**
     * The orderings for the query.
     *
     * @var array
     */
    protected $orders = [];

    protected $limit;

    /**
     * Database connection manager
     *
     * @var Manager
     */
    protected $manager;

    const EXEC_RETURN_ROW_SET = 1;
    const EXEC_RETURN_ROW_COUNT = 2;
    const EXEC_RETURN_LAST_INSERTED_ID = 3;

    public function __construct($database, $table = null)
    {
        $this->database = $database;
        $this->table = $table;
        $this->manager = Manager::getInstance(Bag::get());
    }

    final protected function table($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Set the columns to be selected.
     *
     * @param array|string $columns
     * @return $this
     */
    final protected function select($columns = ['*'])
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();

        return $this;
    }

    /**
     * @param array $wheres
     * @return $this
     */
    final protected function where($wheres)
    {
        $this->wheres = $wheres;

        return $this;
    }


    /**
     * Add an "order by" clause to the query
     *
     * @param $column
     * @param string $order
     * @return $this
     */
    final protected function orderBy($column, $order = 'asc')
    {
        $this->orders[$column] = strtolower($order) === 'asc' ? 'asc' : 'desc';
        return $this;
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param $value
     * @return $this
     */
    final protected function limit($value)
    {
        $this->limit = $value;
        return $this;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array|string $columns
     * @return array returns an array containing all of the remaining rows in the result set.
     *               The array represents each row as an array of column values with properties corresponding to each column name.
     *               An empty array is returned if there are zero results to fetch, or FALSE on failure.
     */
    final protected function get($columns = ['*'])
    {
        if ($this->columns === null) {
            $this->columns = $columns;
        }
        $builtSql = $this->buildSelectSql();
        $sql = $builtSql['sql'];

        echo $sql;
        $inputParameters = $builtSql['inputParameters'];

        return $this->execRowSetReturned($sql, $inputParameters);
    }

    /**
     * Execute the query and get the first result.
     *
     * @param array|string $columns
     * @return array|bool
     *               The array represents a row as an array of column values with properties corresponding to each column name.
     *               Null is returned if there are zero result to fetch, or FALSE on failure.
     */
    final protected function first($columns = ['*'])
    {
        $result = $this->limit(1)->get($columns);

        // failure
        if ($result === false) {
            return false;
        }

        return isset($result[0]) ? $result[0] : null;
    }

    /**
     * Get a single column's value from the first result of a query.
     *
     * @param string $column
     * @return mixed
     */
    final protected function value($column)
    {
        $result = $this->first([$column]);

        if ($result === false) {
            return false;
        }
        if ($result) {
            return $result[$column];
        }

        return null;
    }

    /**
     * Insert a new record into the database.
     *
     * @param array $values
     * @return bool|string Returns the ID of the last inserted row or sequence value, false on failure.
     */
    final protected function insert(array $values)
    {
        $cols = [];
        $markers = [];
        $inputParameters = null;
        foreach ($values as $col => $val) {
            $cols[] = '`' . $col . '`';
            $marker = $this->valueMarker($col);
            $markers[] = $marker;
            $inputParameters[$marker] = $val;
        }

        $sql = 'INSERT INTO ' . $this->tableFilter($this->table)
            . ' (' . implode(', ', $cols) . ') VALUES ('
            . implode(', ', $markers) . ')';

        return $this->execLastInsertIdReturned($sql, $inputParameters);
    }

    /**
     * Update a record in the database.
     *
     * @param array $values
     * @return bool|int returns the number of rows, false on failure.
     */
    final protected function update(array $values)
    {
        $set = [];
        $inputParameters = null;
        foreach ($values as $col => $val) {
            $marker = $this->valueMarker($col);
            $set[] = '`' . $col . '` = ' . $marker;
            $inputParameters[$marker] = $val;
        }

        $parsedWheres = $this->parseWhere($this->wheres);
        $where = '';
        if ($parsedWheres) {
            $where = $parsedWheres['where'];
            if ($parsedWheres['inputParameters']) {
                $inputParameters = array_merge($inputParameters, $parsedWheres['inputParameters']);
            }
        }

        $sql = 'UPDATE ' . $this->tableFilter($this->table) . ' SET '
            . implode(', ', $set);
        $where && $sql = $sql . ' WHERE ' . $where;

        return $this->execRowCountReturned($sql, $inputParameters);
    }

    /**
     * @param array $wheres
     * @return int returns the number of rows that were deleted by the SQL statement you issued.
     *             Return 0 if no rows were affected, false on failure.
     */
    final protected function delete($wheres)
    {
        $parsedWheres = $this->parseWhere($wheres);
        $where = '';
        $inputParameters = null;
        if ($parsedWheres) {
            $where = $parsedWheres['where'];
            $inputParameters = $parsedWheres['inputParameters'];
        }
        $sql = 'DELETE FROM ' . $this->tableFilter($this->table);
        $where && $sql = $sql . ' WHERE ' . $where;

        return $this->execRowCountReturned($sql, $inputParameters);
    }

    /**
     * @param array $wheres
     * @return array
     */
    final protected function count($wheres)
    {
        $result = $this->where($wheres)->first('count(*) as `count`');
        if (isset($result['count'])) {
            return $result['count'];
        }

        return $result;
    }


    private $validExecTypes = [
        self::EXEC_RETURN_ROW_SET,
        self::EXEC_RETURN_ROW_COUNT,
        self::EXEC_RETURN_LAST_INSERTED_ID
    ];

    private function execute($execType, $statement, $inputParameters = null)
    {
        if (!in_array($execType, $this->validExecTypes, true)) {
            throw new RuntimeException('Model: invalid execute type \'' . $execType . '\'');
        }
        $this->reset();

        if ($execType === self::EXEC_RETURN_ROW_SET) {
            $database = $this->database . '::read';
        } else {
            $database = $this->database . '::write';
        }
        $pdo = $this->manager->pdo($database);

        Timer::start('mysql');
        $PDOStatement = $pdo->prepare($statement);
        $PDOStatement->execute($inputParameters);
        Timer::stop('mysql');

        switch ($execType) {
            case self::EXEC_RETURN_ROW_SET:
                $result = $PDOStatement->fetchAll(\PDO::FETCH_ASSOC);
                break;
            case self::EXEC_RETURN_ROW_COUNT:
                $result = $PDOStatement->rowCount();
                break;
            case self::EXEC_RETURN_LAST_INSERTED_ID:
                $result = $pdo->lastInsertId();
                break;
        }

        return $result;
    }

    final protected function execRowCountReturned($statement, $inputParameters = null)
    {
        return $this->execute(self::EXEC_RETURN_ROW_COUNT, $statement, $inputParameters);
    }

    final protected function execRowSetReturned($statement, $inputParameters = null)
    {
        return $this->execute(self::EXEC_RETURN_ROW_SET, $statement, $inputParameters);
    }

    final protected function execLastInsertIdReturned($statement, $inputParameters = null)
    {
        return $this->execute(self::EXEC_RETURN_LAST_INSERTED_ID, $statement, $inputParameters);
    }


    private function valueMarker($column)
    {
        return ':v_' . $column;
    }

    private function whereMarker($column)
    {
        return ':w_' . $column;
    }

    private function buildSelectSql()
    {
        $parsedSelect = $this->parseSelect();

        $parsedWheres = $this->parseWhere();
        $where = '';
        $inputParameters = null;
        if ($parsedWheres) {
            $where = $parsedWheres['where'];
            $inputParameters = $parsedWheres['inputParameters'];
        }

        $order = $this->parseOrders();

        $sql = 'SELECT ' . $parsedSelect . ' FROM ' . $this->tableFilter($this->table);
        $where && $sql = $sql . ' WHERE ' . $where;
        $order && $sql = $sql . ' ORDER BY ' . $order;
        $this->limit && $sql = $sql . ' LIMIT ' . $this->limit;

        return [
            'sql' => $sql,
            'inputParameters' => $inputParameters,
        ];
    }

    private function parseOrders()
    {
        if ($this->orders) {
            foreach ($this->orders as $col => $direction) {
                $orderItems[] = '`' . $col . '` ' . strtoupper($direction);
            }
            return implode(',', $orderItems);
        }

        return '';
    }

    private function parseSelect()
    {
        if (is_string($this->columns)) {
            return $this->columns;
        } else {
            $cols = [];
            foreach ((array)$this->columns as $col) {
                if ($col === '*') {
                    $cols[] = '*';
                } else {
                    $cols[] = '`' . $col . '`';
                }
            }
            return implode(', ', $cols);
        }
    }


    private function parseWhere()
    {
        if (empty($this->wheres)) {
            return null;
        }
        $whereItems = [];
        $inputParameters = null;
        foreach ($this->wheres as $col => $val) {
            $marker = $this->whereMarker($col);
            if (is_array($val)) {
                $express = $this->parseWhereExpression($val);
                $marker .= '_' . $express['exp'];
                switch ($express['exp']) {
                    case 'IN':
                        if (!is_array($express['value'])) {
                            throw new InvalidArgumentException('Model: the value of IN expression should be an array');
                        }
                        $markerList = [];
                        foreach ($express['value'] as $key => $value) {
                            $inMarker = $marker . '_' . $key;
                            $inputParameters[$inMarker] = $value;
                            $markerList[] = $inMarker;
                        }
                        $whereItems[] = '`' . $col . '` ' . $express['symbol'] . ' (' . implode(',', $markerList) . ')';
                        break;

                    case 'JSON_CONTAINS':
                        $whereItems[] = $express['symbol'] . '(' . $col . ', ' . $express['value'] . ')';
                        break;

                    case 'JSON_SEARCH':
                        if (!is_array($express['value'])) {
                            throw new InvalidArgumentException('Model: the value of JSON_SEARCH expression must be an array');
                        }

                        list($quantity, $needle) = $express['value'];

                        if (!in_array($quantity, Str::quoteWith(['one', 'all']), true)) {
                            throw new InvalidArgumentException('Model: the 1st item of JSON_SEARCH expression\'value must be \'one\' or \'all\'');
                        }

                        $whereItems[] = $express['symbol'] . '(' . $col . ', ' . $quantity . ', ' . $needle . ') IS NOT NULL';
                        break;

                    default:
                        $whereItems[] = '`' . $col . '` ' . $express['symbol'] . ' ' . $marker;
                        $inputParameters[$marker] = $express['value'];
                        break;
                }
            } else {
                $whereItems[] = '`' . $col . '` = ' . $marker;
                $inputParameters[$marker] = $val;
            }
        }

        return [
            'where' => implode(' AND ', $whereItems),
            'inputParameters' => $inputParameters,
        ];
    }

    private $validWhereExpressions = [
        'EQ' => '=',
        'NEQ' => '<>',
        'GT' => '>',
        'EGT' => '>=',
        'LT' => '<',
        'LIKE' => 'LIKE',
        'IN' => 'IN',
        'JSON_CONTAINS' => 'JSON_CONTAINS',
        'JSON_SEARCH' => 'JSON_SEARCH',
    ];

    private function parseWhereExpression($exp)
    {
        if (!is_array($exp) || count($exp) !== 2 || !isset($exp[0]) || !isset($exp[1])) {
            throw new InvalidArgumentException('Model: invalid mysql where expression: ' . json_encode($exp));
        }
        $express = strtoupper($exp[0]);
        if (!array_key_exists($express, $this->validWhereExpressions)) {
            throw new InvalidArgumentException('Model: invalid mysql where expression: ' . $exp[0]);
        }

        $symbol = $this->validWhereExpressions[$express];
        if (strpos($symbol, 'JSON') === 0) {
            $exp[1] = Str::quoteWith($exp[1]);
        }

        return [
            'exp' => $express,
            'symbol' => $symbol,
            'value' => $exp[1],
        ];
    }

    private function tableFilter($table)
    {
        if (strpos($table, '`') === false) {
            if (strpos($table, '.') !== false) {
                $table = str_replace('.', '`.`', $table);
            }
            $table = '`' . $table . '`';
        }
        return $table;
    }

    private function reset()
    {
        $this->columns = null;
        $this->wheres = null;
        $this->orders = [];
        $this->limit = null;
    }
}