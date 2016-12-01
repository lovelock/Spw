<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/30/16
 * Time: 4:42 PM
 */

namespace Spw;


use PDO;
use PDOException;

class StatementBuilder
{
    /**
     * @param \PDOStatement $statement
     * @param array $params
     * @return \PDOStatement
     * @throws PDOException
     */
    public static function bindParams(\PDOStatement $statement, array $params)
    {
        if (null === $statement) {
            throw new PDOException('PDOStatement must not be null');
        }

        foreach ($params as $k => $v) {
            if (is_bool($v)) {
                $statement->bindValue($k, $v, PDO::PARAM_BOOL);
            } else if (is_numeric($v)) {
                $statement->bindValue($k, $v, PDO::PARAM_INT);
            } else {
                $statement->bindValue($k, $v, PDO::PARAM_STR);
            }
        }

        return $statement;
    }
}