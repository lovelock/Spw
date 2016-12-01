<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 12/1/16
 * Time: 3:23 PM
 */

namespace Tests;

use Spw\Config\DevConfig;
use Spw\Connection;

class ConnectionInsertTest extends \PHPUnit_Framework_TestCase
{
    public function testInsertCommonValues()
    {
        $conn = new Connection(new DevConfig());
        $id = $conn->into('staffs')
            ->insert([
                'name' => 'wangqingchun',
                'address' => 'Beijing',
                'email' => 'frostwong@gmail.com',
            ]);

        var_export($id);
    }
}
