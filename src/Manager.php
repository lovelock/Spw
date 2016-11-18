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
use Spw\Support\Str;

class Manager
{
    /**
     * The active connection instances.
     *
     * @var array
     */
    protected $connections = [];

    /**
     * The config of database.
     *
     * @var array
     */
    protected $config;

    /**
     * The Manager instance.
     *
     * @var Manager
     */
    private static $instance = null;

    protected function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Get Manager instance.
     *
     * @param array|null $config
     * @return Manager
     */
    public static function getInstance($config = null)
    {
        if (self::$instance === null) {
            self::$instance = new Manager($config);
        }
        return self::$instance;
    }

    /**
     * Get pdo object
     * @param $pdoName
     * @return bool|null|\PDO
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function pdo($pdoName)
    {
        list($databaseName, $type) = $this->parsePdoName($pdoName);
        if (null === $type) {
            throw new InvalidArgumentException('Invalid pdo name \'' . $pdoName . '\'');
        }
        $connection = $this->connection($databaseName);

        if ($type === 'read') {
            if ($readPdo = $connection->getReadPdo()) {
                return $readPdo;
            } else if ($pdo = $connection->getWritePdo()) {
                return $pdo;
            }
        } else if ($type === 'write' && ($pdo = $connection->getWritePdo())) {
            return $pdo;
        }
        throw new RuntimeException("Manager: PDO [$pdoName] not found");
    }

    /**
     * Get a database connection instance.
     *
     * @param string $databaseName
     * @return Connection
     * @throws \InvalidArgumentException
     */
    private function connection($databaseName)
    {
        if (!isset($this->connections[$databaseName])) {
            $this->connections[$databaseName] = $this->makeConnection($databaseName);
        }

        return $this->connections[$databaseName];
    }

    private function parsePdoName($pdoName)
    {
        return Str::endsWith($pdoName, ['::read', '::write'])
            ? explode('::', $pdoName, 2) : [$pdoName, null];
    }

    /**
     * Make the database connection instance.
     * @param $databaseName
     * @return Connection
     * @throws InvalidArgumentException
     */
    private function makeConnection($databaseName)
    {
        $config = $this->getConfig($databaseName);
        return new Connection($config);
    }

    /**
     * Get the configuration for connection.
     *
     * @param  string $databaseName
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    private function getConfig($databaseName)
    {
        if (!$this->config[$databaseName]) {
            throw new InvalidArgumentException("Manager: database [$databaseName] not configured");
        }
        return $this->config[$databaseName];
    }
}