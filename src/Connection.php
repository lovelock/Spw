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

class Connection
{
    /**
     * The config of database.
     *
     * @var array
     */
    protected $config;

    /**
     * The active PDO connection.
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * The active PDO connection used for reads.
     *
     * @var PDO
     */
    protected $readPdo;

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

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param $config
     * @return PDO
     * @throws \PDOException
     */
    private function connect($config)
    {
        try {
            $dsn = "mysql:dbname={$config['database']};host={$config['host']};port={$config['port']};charset={$config['charset']}";

            $pdo = new \PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $this->options
            );

            $pdo->exec('set names ' . $config['charset']);
        } catch (PDOException $e) {
            trigger_error(
                'Errors on connecting mysql(' . $dsn . ' username=' . $config['username'] . ')',
                E_USER_WARNING
            );
            throw $e;
        }
        return $pdo;
    }

    final public function getWritePdo()
    {
        if ($this->pdo !== null) {
            return $this->pdo;
        }

        $this->pdo = $this->getPdo('write');
        return $this->pdo;
    }

    final public function getReadPdo()
    {
        if ($this->readPdo !== null) {
            return $this->readPdo;
        }

        $this->readPdo = $this->getPdo('read');
        return $this->readPdo;
    }

    private function getPdo($type)
    {
        $config = $this->getConfig($type);
        if (empty($config)) {
            return null;
        }

        return $this->connect($config) ?: null;
    }

    private function getConfig($type = 'write')
    {
        if (!isset($this->config[$type]) || empty($this->config[$type])) {
            return [];
        }
        $host = $this->config[$type][array_rand($this->config[$type], 1)];
        return [
            'host' => $host,
            'port' => $this->config['port'],
            'database' => $this->config['database'],
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'charset' => $this->config['charset'],
        ];
    }
}