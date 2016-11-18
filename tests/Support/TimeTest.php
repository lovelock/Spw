<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/17/16
 * Time: 7:12 PM
 */

namespace Tests\Spw\Support;

use Spw\Support\Time;

class TimeTest extends \PHPUnit_Framework_TestCase
{
    public function testMillisecond()
    {
        echo Time::millisecond() * 1000;
    }
}
