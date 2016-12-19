<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 12/19/16
 * Time: 3:18 PM
 */

namespace Tests;


use Spw\Config\DevConfig;
use Spw\Connection\Connection;

class ConnectionRawTest extends \PHPUnit_Framework_TestCase
{
    public function testSelect()
    {
        $conn = new Connection(new DevConfig());
        $sql = 'select * from books';
        $actual = $conn->raw($sql);
        $expected = [
            0 =>
                [
                    'id' => 1,
                    'name' => 'Code Complete',
                    'tags' => '["development", "software engineering"]',
                ],
            1 =>
                [
                    'id' => 2,
                    'name' => 'Core Java',
                    'tags' => '["java", "development"]',
                ],
            2 =>
                [
                    'id' => 3,
                    'name' => 'Thinking in Python',
                    'tags' => '["python", "development"]',
                ],
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testInsert()
    {
        $conn = new Connection(new DevConfig());
        $email = 'sdkfsdfj@kkggk.com'. random_int(1, 10000);
        $sql = 'insert into staffs (`name`, `email`, `address`) values(\'xxkdkdsjsadkf\', \'' . $email . '\', \'badasdfs\')';
        $this->assertGreaterThan(1, $conn->raw($sql));
    }

    public function testUpdate()
    {
        $conn = new Connection(new DevConfig());
        $sql = 'update staffs set `name` = "dkfasdfjaksjdfas" WHERE `id` = 44';
        $this->assertGreaterThanOrEqual(0, $conn->raw($sql));
    }

    public function testDelete()
    {
        $conn = new Connection(new DevConfig());
        $sql = 'delete from staffs where `id` = 41';

        $this->assertGreaterThanOrEqual(0, $conn->raw($sql));
    }
}
