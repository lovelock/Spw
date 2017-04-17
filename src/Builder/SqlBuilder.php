<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/29/16
 * Time: 11:54 PM
 */

namespace Spw\Builder;


use \InvalidArgumentException;
use Spw\Connection\ConnectionInterface;
use Spw\Support\Arrays;
use Spw\Support\Str;

class SqlBuilder implements SqlBuilderInterface
{
    /**
     * @param ConnectionInterface $connection
     * @return mixed
     */
    public static function buildSelectSql(ConnectionInterface $connection)
    {
        $sql = self::parseSelectColumns($connection);
        $sql .= self::parseCount($connection);
        $sql .= self::parseFrom($connection);
        $parsedWhere = self::parseWhere($connection);
        if (is_array($parsedWhere)) {
            $where = $parsedWhere['where'];
            $parameters = $parsedWhere['inputParameters'];
            $sql .= $where;

            $sql .= self::parseGroupBy($connection);
            $sql .= self::parseOrderBy($connection);
            $sql .= self::parseLimit($connection);

            return [
                $sql,
                $parameters,
            ];
        }

        $sql .= self::parseGroupBy($connection);
        $sql .= self::parseOrderBy($connection);
        $sql .= self::parseLimit($connection);

        return $sql;
    }

    public static function buildRowCountSql(ConnectionInterface $connection)
    {
        $sql = 'SELECT COUNT(1) AS `total_count`';
        $sql .= self::parseFrom($connection);
        $parsedWhere = self::parseWhere($connection);

        if (is_array($parsedWhere)) {
            $where = $parsedWhere['where'];
            $parameters = $parsedWhere['inputParameters'];
            $sql .= $where;

            return [
                $sql,
                $parameters,
            ];
        }

        return $sql;
    }

    /**
     * @param ConnectionInterface $connection
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public static function buildInsertSql(ConnectionInterface $connection)
    {
        $sql = 'INSERT INTO ' . $connection->getTable();

        $values = $connection->getValues();
        $inputParams = [];

        //todo 检查是否是嵌套数组，如果是，说明就是批量写入，如果不是嵌套，就是写入一条
        if (Arrays::isNested($values)) {
            $cols = Str::quoteWith(array_keys($values[0]), '`');
            $sql .= ' (' . implode(', ', $cols) . ') VALUES ';

            foreach ($values as $i => $value) {
                $sql .= '(';
                foreach ($value as $k => $item) {
                    $sql .= ':' . $k . '_' . $i . ', ';
                    $inputParams[':' . $k . '_' . $i] = $item;
                }
                $sql = rtrim($sql, ', ');
                $sql .= '),';
            }
        } else {
            $sql .= ' SET ';
            foreach ($values as $key => $val) {
                $markedKey = self::valueMarker($key);
                $sql .= Str::quoteWith($key, '`') . ' = ' . $markedKey . ', ';
                $inputParams[$markedKey] = $val;
            }
        }
        $sql = rtrim($sql, ', ') . ' ';

        return [
            $sql,
            $inputParams,
        ];
    }

    /**
     * @param ConnectionInterface $connection
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public static function buildReplaceSql(ConnectionInterface $connection)
    {
        $sql = 'REPLACE INTO ' . $connection->getTable();

        $values = $connection->getValues();
        $inputParams = [];

        //todo 检查是否是嵌套数组，如果是，说明就是批量写入，如果不是嵌套，就是写入一条
        if (Arrays::isNested($values)) {
            $cols = Str::quoteWith(array_keys($values[0]), '`');
            $sql .= ' (' . implode(', ', $cols) . ') VALUES ';

            foreach ($values as $i => $value) {
                $sql .= '(';
                foreach ($value as $k => $item) {
                    $sql .= ':' . $k . '_' . $i . ', ';
                    $inputParams[':' . $k . '_' . $i] = $item;
                }
                $sql = rtrim($sql, ', ');
                $sql .= '),';
            }
        } else {
            $sql .= ' SET ';
            foreach ($values as $key => $val) {
                $markedKey = self::valueMarker($key);
                $sql .= Str::quoteWith($key, '`') . ' = ' . $markedKey . ', ';
                $inputParams[$markedKey] = $val;
            }
        }
        $sql = rtrim($sql, ', ') . ' ';

        return [
            $sql,
            $inputParams,
        ];
    }


    /**
     * @param ConnectionInterface $connection
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public static function buildUpdateSql(ConnectionInterface $connection)
    {
        $sql = 'UPDATE ' . $connection->getTable() . ' SET ';

        $values = $connection->getValues();
        $inputParams = [];

        foreach ($values as $key => $val) {
            $markedKey = self::valueMarker($key);
            $sql .= Str::quoteWith($key, '`') . ' = ' . $markedKey . ', ';
            $inputParams[$markedKey] = $val;
        }

        $sql = rtrim($sql, ', ');

        $parsedWhere = self::parseWhere($connection);

        $sql .= $parsedWhere['where'];

        $inputParams = array_merge($inputParams, $parsedWhere['inputParameters']);

        return [
            $sql,
            $inputParams,
        ];
    }

    /**
     * @param ConnectionInterface $connection
     * @return mixed
     */
    public static function buildDeleteSql(ConnectionInterface $connection)
    {
        $sql = 'DELETE FROM ' . $connection->getTable();

        $parsedWhere = self::parseWhere($connection);
        $sql .= $parsedWhere['where'];
        $inputParams = $parsedWhere['inputParameters'];

        return [
            $sql,
            $inputParams,
        ];
    }

    private static function parseLimit(ConnectionInterface $connection)
    {
        $limits = $connection->getLimit();
        if (empty($limits)) {
            return '';
        }

        $offset = (int)$limits['offset'];
        $count = (int)$limits['count'];

        if ($count === 0) {
            return ' LIMIT ' . $offset;
        } else {
            return ' LIMIT ' . $offset . ', ' . $count;
        }
    }

    private static function parseSelectColumns(ConnectionInterface $connection)
    {
        $columns = $connection->getColumns();
        if (!$columns) {
            return 'SELECT ';
        }

        if ($columns === '*') {
            $implodedColumns = $columns;
        } else {
            if (is_array($columns)) {
                $quotedColumns = Str::quoteWith($columns, '`');
                $implodedColumns = implode(', ', $quotedColumns);
            } else {
                $implodedColumns = Str::quoteWith($columns, '`');
            }
        }

        return 'SELECT ' . $implodedColumns;
    }

    private static function parseFrom(ConnectionInterface $connection)
    {
        if (!($table = $connection->getTable())) {
            throw new InvalidArgumentException('Database table must be valid, ' . $table . ' is given.');
        }

        if (!is_string($table)) {
            throw new InvalidArgumentException('Database table must be a string ' . $table . ' is given.');
        }

        return ' FROM ' . $table;
    }

    private static function parseGroupBy(ConnectionInterface $connection)
    {
        if ('' === $groupBy = $connection->getGroupBy()) {
            return '';
        }

        return ' GROUP BY ' . $groupBy . ' ';
    }

    private static function parseOrderBy(ConnectionInterface $connection)
    {
        if ([] === ($orderBys = $connection->getOrderBy())) {
            return '';
        }

        $implodedOrderBys = ' ORDER BY ';
        foreach ($orderBys as $orderBy) {
            $implodedOrderBys .= implode(' ', $orderBy) . ', ';
        }

        return rtrim($implodedOrderBys, ', ');
    }

    private static $validWhereSymbols = [
        '>' => 'gt',
        '>=' => 'gte',
        '=' => 'eq',
        '<=' => 'lte',
        '<' => 'lt',
        '<>' => 'neq',
        '!=' => 'neq',
        'BETWEEN' => 'between',
        'LIKE' => 'LIKE',
        'IN' => 'IN',
        'NOT IN' => 'NOT_IN',
        'JSON_CONTAINS' => 'JSON_CONTAINS',
        'JSON_SEARCH' => 'JSON_SEARCH',
    ];


    private static function parseWhere(ConnectionInterface $connection)
    {
        if (empty($wheres = $connection->getWheres())) {
            return '';
        }

        $whereItems = [];
        $inputParameters = null;
        foreach ($wheres as $col => $val) {
            if (!strpos($col, '.')) {
                $marker = self::whereMarker($col);
                if (is_array($val)) {
                    $expression = self::parseWhereExpression($val);
                    $marker .= '_' . self::$validWhereSymbols[$expression['symbol']];
                    switch ($expression['symbol']) {
                        case 'IN':
                            if (!is_array($expression['value'])) {
                                throw new InvalidArgumentException('Value of IN expression should be an array');
                            }
                            $markerList = [];
                            foreach ($expression['value'] as $key => $value) {
                                $inMarker = $marker . '_' . $key;
                                $inputParameters[$inMarker] = $value;
                                $markerList[] = $inMarker;
                            }
                            $whereItems[] = Str::quoteWith($col,
                                    '`') . ' ' . $expression['symbol'] . ' (' . implode(', ',
                                    $markerList) . ')';
                            break;
                        case 'BETWEEN':
                            if (!is_array($expression['value'])) {
                                throw new InvalidArgumentException('Value of BETWEEN expression should be an array');
                            }
                            $markerList = [];
                            foreach ($expression['value'] as $key => $value) {
                                $betweenMarker = $marker . '_' . $key;
                                $inputParameters[$betweenMarker] = $value;
                                $markerList[] = $betweenMarker;
                            }
                            $whereItems[] = Str::quoteWith($col, '`') . ' ' . $expression['symbol'] . ' ' . $markerList[0] . ' AND ' . $markerList[1];
                            break;

                        case 'JSON_CONTAINS':
                            $whereItems[] = $expression['symbol'] . '(' . Str::quoteWith($col,
                                    '`') . ', ' . $marker . ')';
                            $inputParameters[$marker] = $expression['value'];
                            break;

                        case 'JSON_SEARCH':
                            if (!is_array($expression['value'])) {
                                throw new InvalidArgumentException('Model: the value of JSON_SEARCH expression must be an array');
                            }

                            list($quantity, $needle) = $expression['value'];

                            if (!in_array($quantity, Str::quoteWith(['one', 'all'], '"'), true)) {
                                throw new InvalidArgumentException('Model: the 1st item of JSON_SEARCH expression\'value must be \'one\' or \'all\'');
                            }

                            $whereItems[] = $expression['symbol'] . '(' . Str::quoteWith($col,
                                    '`') . ', ' . $quantity . ', ' . $marker . ') IS NOT NULL';
                            $inputParameters[$marker] = $needle;
                            break;

                        default:
                            $whereItems[] = Str::quoteWith($col, '`') . ' ' . $expression['symbol'] . ' ' . $marker;
                            $inputParameters[$marker] = $expression['value'];
                            break;
                    }
                } else {
                    $whereItems[] = Str::quoteWith($col, '`') . ' = ' . $marker;
                    $inputParameters[$marker] = $val;
                }
            } else { // JSON related
                $explodedCol = explode('.', $col);

                $jsonCol = $explodedCol[0];
                unset($explodedCol[0]);
                $marker = self::whereMarker($jsonCol);

                $jsonCol .= '->"$';
                foreach ($explodedCol as $v) {
                    if (is_numeric($v)) {
                        $jsonCol .= '[' . $v . ']';
                    } else {
                        if (is_string($v)) {
                            $jsonCol .= '.' . $v;
                        }
                    }
                }
                $jsonCol .= '"';

                if (is_array($val)) {
                    $expression = self::parseWhereExpression($val);
                    $marker .= '_' . self::$validWhereSymbols[$expression['symbol']];
                    $whereItems[] = $jsonCol . ' ' . $expression['symbol'] . ' ' . $marker;
                    $inputParameters[$marker] = $expression['value'];
                } else {
                    $marker .= '_' . 'equals';
                    $whereItems[] = $jsonCol . ' = ' . $marker;
                    $inputParameters[$marker] = $val;
                }

            }
        }

        return [
            'where' => ' WHERE ' . implode(' AND ', $whereItems),
            'inputParameters' => $inputParameters,
        ];
    }

    private static function whereMarker($column)
    {
        return ':w_' . $column;
    }

    private static function valueMarker($column)
    {
        return ':v_' . $column;
    }

    private static function parseWhereExpression($array)
    {
        if (!is_array($array) || count($array) !== 2 || !isset($array[0]) || !isset($array[1])) {
            throw new InvalidArgumentException('Invalid where expression: ' . json_encode($array));
        }

        list($symbol, $value) = $array;
        if (!array_key_exists(strtoupper($symbol), self::$validWhereSymbols)) {
            throw new InvalidArgumentException('Invalid where expression: ' . $symbol);
        }

        if (strpos($symbol, 'JSON') === 0) {
            $value[0] = Str::quoteWith($value[0], '"');
        }

        return [
            'symbol' => strtoupper($symbol),
            'value' => $value,
        ];
    }

    private static function parseCount(ConnectionInterface $connection)
    {
        $counts = $connection->getCounts();
        if (!$counts) {
           return '';
        }
        $sql = '';
        foreach ($counts as $count) {
            $tmp = ' COUNT(' . Str::quoteWith($count[0], '`') . ')';
            if ($count[2]) {
                $tmp = 'DISTINCT(' . $tmp . ')';
            }
            if ($count[1]) {
                $tmp .= ' AS ' . Str::quoteWith($count[1], '`');
            }
            $sql .= $tmp . ',';
        }

        if ($connection->getColumns()) {
            return ', ' . rtrim($sql, ',');
        } else {
            return rtrim($sql, ',');
        }
    }

}