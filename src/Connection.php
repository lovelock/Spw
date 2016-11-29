<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/16/16
 * Time: 4:47 PM
 */

namespace Spw;


use PDO;
use PDOException;
use Spw\Config\ConfigInterface;

class Connection implements ConnectionInterface
{
    /**
     * The config of database.
     *
     * @var ConfigInterface
     */
    protected $config;

    /**
     * The active PDO connection.
     *
     * @var PDO
     */
    protected $pdo;

    protected $table;

    protected $columns;


    /**
     * The default PDO connection timeout
     * @var int
     */
    const TIME_OUT = 2;

    /**
     * The default PDO connection options
     * @var array
     */
    protected $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => self::TIME_OUT,
    ];

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return PDO
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

        try {
            $pdo = new \PDO(
                $dsn,
                $this->config->getUserName(),
                $this->config->getPassword(),
                $this->options
            );

            $pdo->exec('SET NAMES ' . $this->config->getDefaultCharset());
        } catch (PDOException $e) {
            trigger_error(
                'Errors on connecting mysql(' . $dsn . ' username=' . $this->config->getUserName() . ')',
                E_USER_WARNING
            );
            throw $e;
        }
        return $pdo;
    }

    public function from(string $table)
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

        $sql = StatementBuilder::buildSelectStatement($this);

        return $this->connect()->prepare($sql)->fetchAll();
    }

    /**
     * @param array $columns
     * @return mixed
     * @throws \PDOException
     */
    public function selectOne($columns = ['*'])
    {
        // TODO: Implement selectOne() method.
    }

    /**
     * @param array $values
     * @return mixed
     * @throws \PDOException
     */
    public function insert(array $values)
    {
        // TODO: Implement insert() method.
    }

    /**
     * @param array $values
     * @return mixed
     * @throws \PDOException
     */
    public function update(array $values)
    {
        // TODO: Implement update() method.
    }

    /**
     * @param array $bindings
     * @return mixed
     */
    public function prepareBinds(array $bindings)
    {
        // TODO: Implement prepareBinds() method.
    }
}