<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 12/15/16
 * Time: 10:47 AM
 */

namespace Spw;


use Spw\Config\ConfigInterface;

class PdoFactory
{
    private static $pdoBag = [];
    
    public static function makePdo(ConfigInterface $config, array $options)
    {
        if (!isset(self::$pdoBag[$config->getDatabaseName()])) {
            self::$pdoBag[$config->getDatabaseName()] = self::newPdo($config, $options);
        }

        return self::$pdoBag[$config->getDatabaseName()];
    }
    
    private static function newPdo(ConfigInterface $config, array $options = [])
    {
        $dsn = sprintf('%s:dbname=%s;host=%s;port=%d;charset=%s', $config->getRMDBSName(),
            $config->getDatabaseName(),
            $config->getHost(),
            $config->getPort(),
            $config->getDefaultCharset()
        );

        $pdo = new \PDO(
            $dsn,
            $config->getUserName(),
            $config->getPassword(),
            $options
        );

        $pdo->exec('SET NAMES ' . $config->getDefaultCharset());

        return $pdo;
    }
}