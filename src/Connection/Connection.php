<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/16/16
 * Time: 4:47 PM
 */

namespace Spw\Connection;


use InvalidArgumentException;
use PDO;
use Spw\Builder\SqlBuilder;
use Spw\Builder\StatementBuilder;
use Spw\Config\ConfigInterface;
use Spw\PdoFactory;
use Spw\Support\Str;

class Connection implements ConnectionInterface
{
    /**
     * The config of database.
     *
     * @var ConfigInterface
     */
    private $config;

    private $table;

    private $columns;

    private $limitOffset;

    private $limitCount;

    private $wheres;

    private $orderBy = [];

    private $values = [];

    private $counts = [];

    private $groupBy;


    /**
     * The default PDO connection timeout
     * @var int
     */
    const TIME_OUT = 2;

    /**
     * The default PDO connection options
     * @var array
     */
    private $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => self::TIME_OUT,
    ];

    const EXEC_RETURN_ROW_SET = 1;
    const EXEC_RETURN_ROW_COUNT = 2;
    const EXEC_RETURN_LAST_INSERT_ID = 3;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getTable()
    {
        return Str::quoteWith($this->table, '`');
    }

    /**
     * @return PDO
     * @throws \PDOException
     * @internal param $config
     */
    private function connect()
    {
        return PdoFactory::makePdo($this->config, $this->options);
    }

    public function from($table)
    {
        $this->reset();
        $this->table = $table;

        return $this;
    }

    /**
     * @param string|array $columns
     * @return mixed
     * @throws \PDOException
     */
    public function select($columns = '*')
    {
        $this->columns = $columns;

        $builtSql = SqlBuilder::buildSelectSql($this);

        if (is_array($builtSql)) { // Parameters need to be bound, \PDOStatement::execute() must be called.
            $preparedSth = $this->connect()->prepare($builtSql[0]);
            $boundSth = StatementBuilder::bindValues($preparedSth, $builtSql[1]);
            $boundSth->execute();
        } else { // Simple \PDO::query() method is called.
            $boundSth = $this->connect()->query($builtSql);
        }

        return $boundSth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array $wheres
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function where($wheres = [])
    {
        if (!is_array($wheres)) {
            throw new InvalidArgumentException('Where condition must be an array, string ' . $wheres . ' is given.');
        }
        $this->wheres = $wheres;
        return $this;
    }

    public function limit($offset, $count = 0)
    {
        $this->limitOffset = $offset;
        $this->limitCount = $count;

        return $this;
    }

    /**
     * @param array|string $columns
     * @return mixed
     * @throws \PDOException
     */
    public function selectOne($columns = '*')
    {
        return $this->limit(1)->select($columns)[0];
    }

    /**
     * @param array $values
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \PDOException
     */
    public function insert(array $values)
    {
        $this->values = $values;

        list($sql, $inputParams) = SqlBuilder::buildInsertSql($this);
        $preparedSth = $this->connect()->prepare($sql);
        $boundSth = StatementBuilder::bindValues($preparedSth, $inputParams);
        $boundSth->execute();
        return $this->connect()->lastInsertId();
    }

    /**
     * @param array $values
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \PDOException
     */
    public function replace(array $values)
    {
        $this->values = $values;

        list($sql, $inputParams) = SqlBuilder::buildReplaceSql($this);
        $preparedSth = $this->connect()->prepare($sql);
        $boundSth = StatementBuilder::bindValues($preparedSth, $inputParams);
        $boundSth->execute();
        return $this->connect()->lastInsertId();
    }

    /**
     * @param array $values
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \PDOException
     */
    public function update(array $values)
    {
        $this->values = $values;

        list($sql, $inputParams) = SqlBuilder::buildUpdateSql($this);
        $preparedSth = $this->connect()->prepare($sql);
        $boundSth = StatementBuilder::bindValues($preparedSth, $inputParams);
        $boundSth->execute();
        return $boundSth->rowCount();
    }


    /**
     * @return bool
     * @throws \PDOException
     */
    public function delete()
    {
        list($sql, $inputParams) = SqlBuilder::buildDeleteSql($this);
        $preparedSth = $this->connect()->prepare($sql);
        $boundSth = StatementBuilder::bindValues($preparedSth, $inputParams);
        $boundSth->execute();
        return $boundSth->rowCount();
    }

    public function orderBy($column, $asc = 'desc')
    {
        $this->orderBy[] = [$column, $asc];
        return $this;
    }

    public function groupBy($column)
    {
        $this->groupBy = $column;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        $limit = [];
        if (null !== $this->limitOffset) {
            $limit['offset'] = $this->limitOffset;
        }
        if (null !== $this->limitCount) {
            $limit['count'] = $this->limitCount;
        }
        return $limit;
    }

    /**
     * @return mixed
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @return string
     */
    public function getGroupBy()
    {
        return $this->groupBy;
    }

    /**
     * @return array
     */
    public function getWheres()
    {
        return $this->wheres;
    }


    /**
     * @param $sql
     * @return \PDOStatement
     * @throws \PDOException
     */
    public function query($sql)
    {
        return $this->connect()->prepare($sql);
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param $table
     * @return $this
     */
    public function into($table)
    {
        $this->from($table);

        return $this;
    }

    /**
     * Count specified column.
     *
     * @param string $col
     * @param string $alias
     * @param bool $distinct
     * @return mixed
     */
    public function count($col, $alias, $distinct = false)
    {
        $this->counts[] = [$col, $alias, $distinct];

        return $this;
    }

    /**
     * Get count clause of a select SQL statement.
     *
     * @return array
     */
    public function getCounts()
    {
        return $this->counts;
    }


    /**
     * Run raw sql and return result.
     *
     * @param $sql
     * @param array $params
     * @return array|int
     * @throws \PDOException
     */
    public function raw($sql, array $params)
    {
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute($params);

        if (stripos($sql, 'select') === 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            if (stripos($sql, 'update') === 0 || stripos($sql, 'delete') === 0) {
                return $stmt->execute();
            } else {
                if (stripos($sql, 'insert') === 0) {
                    return $this->connect()->lastInsertId();
                }
            }
        }
    }


    /**
     * Get number of rows of specific condition.
     *
     * @return integer
     * @throws \PDOException
     */
    public function getNumRows()
    {
        $builtSql = SqlBuilder::buildRowCountSql($this);

        if (is_array($builtSql)) {
            $preparedSth = $this->connect()->prepare($builtSql[0]);
            $boundSth = StatementBuilder::bindValues($preparedSth, $builtSql[1]);
            $boundSth->execute();
        } else {
            $boundSth = $this->connect()->query($builtSql);
        }

        $result = $boundSth->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total_count'];
    }

    public function reset()
    {
        $this->columns = '';
        $this->limitCount = null;
        $this->limitOffset = null;
        $this->wheres = [];
        $this->orderBy = [];
        $this->values = [];
        $this->counts = [];
    }
}