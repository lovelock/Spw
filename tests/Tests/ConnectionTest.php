<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/30/16
 * Time: 12:01 AM
 */

namespace Tests;


use PHPUnit_Framework_TestCase;
use Spw\Config\DevConfig;
use Spw\Connection;


class ConnectionTest extends PHPUnit_Framework_TestCase
{
    public function testSelect()
    {
        $conn = new Connection(new DevConfig());
        $result = $conn->from('books')
            ->select();

        var_export($result);
    }
}
