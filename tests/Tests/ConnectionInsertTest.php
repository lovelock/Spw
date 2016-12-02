<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 12/1/16
 * Time: 3:23 PM
 */

namespace Tests;

use Spw\Config\DevConfig;
use Spw\Connection\Connection;

class ConnectionInsertTest extends \PHPUnit_Framework_TestCase
{
    public function testInsertCommonValues()
    {
        $conn = new Connection(new DevConfig());
        $id = $conn->into('staffs')
            ->insert([
                'name' => 'wangqingchun',
                'address' => 'Beijing',
                'email' => 'frostwong@gmail.com' . random_int(1, 10000),
            ]);

        $this->assertTrue($id);
    }

    public function testMultiInsert()
    {
        $conn = new Connection(new DevConfig());
        $id = $conn->into('staffs')
            ->insert([
                [
                    'name' => 'xxxx',
                    'address' => 'kdkdkdkd',
                    'email' => 'dskfjaskdfj@mail.com' . random_int(1, 10000),
                ],
                [
                    'name' => 'xxxx',
                    'address' => 'kdkdkdkd',
                    'email' => 'dskfjaskdfj@mail.com' . random_int(1, 10000),
                ],
            ]);

        $this->assertTrue($id);
    }
}
