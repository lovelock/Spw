<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 12/2/16
 * Time: 11:26 AM
 */

namespace Tests;


use Spw\Config\DevConfig;
use Spw\Connection\Connection;


class ConnectionUpdateTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdate()
    {
        $conn = new Connection(new DevConfig());
        $rowsAffected = $conn->from('staffs')
            ->where([
                'id' => 1,
            ])
            ->update([
                'email' => 'frostwong@gmail.com' . random_int(1, 100000),
            ]);

        $this->assertGreaterThanOrEqual(0, $rowsAffected);
    }
}
