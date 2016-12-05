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

    private $limit;

    private $wheres;

    private $orderBy = [];

    private $values = [];


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
        $dsn = sprintf('%s:dbname=%s;host=%s;port=%d;charset=%s', $this->config->getRMDBSName(),
            $this->config->getDatabaseName(),
            $this->config->getHost(),
            $this->config->getPort(),
            $this->config->getDefaultCharset()
        );

        $pdo = new \PDO(
            $dsn,
            $this->config->getUserName(),
            $this->config->getPassword(),
            $this->options
        );

        $pdo->exec('SET NAMES ' . $this->config->getDefaultCharset());

        return $pdo;
    }

    public function from($table)
    {
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

    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param array|string $columns
     * @return mixed
     * @throws \PDOException
     */
    public function selectOne($columns = '*')
    {
        return $this->limit(1)->select($columns);
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
        return $boundSth->execute();
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
        return $boundSth->execute();
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
        return $boundSth->execute();
    }

    /**
     * @param array $bindings
     * @return mixed
     */
    public function prepareBinds(array $bindings)
    {
        // TODO: Implement prepareBinds() method.
    }

    public function orderBy($column, $asc = 'desc')
    {
        $this->orderBy[] = [$column, $asc];
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return mixed
     */
    public function getOrderBy()
    {
        return $this->orderBy;
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
        $this->table = $table;

        return $this;
    }
}