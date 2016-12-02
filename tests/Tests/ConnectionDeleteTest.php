<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 12/2/16
 * Time: 2:22 PM
 */

namespace Tests;


use Spw\Config\DevConfig;
use Spw\Connection\Connection;


class ConnectionDeleteTest extends \PHPUnit_Framework_TestCase
{
    public function testDelete()
    {
        $conn = new Connection(new DevConfig());
        $id = $conn->from('staffs')
            ->where([
                'id' => '5',
            ])
            ->delete();

        $this->assertTrue($id);
    }
}
