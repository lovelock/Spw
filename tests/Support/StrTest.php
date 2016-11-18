<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/16/16
 * Time: 5:12 PM
 */

namespace Tests\Spw\Support;

use Spw\Support\Str;

class StrTest extends \PHPUnit_Framework_TestCase
{
    public function testStartWithEmptyString()
    {
        $this->assertFalse(Str::startsWith('test', ''));
    }

    public function testStartWithElementInArray()
    {
        $this->assertTrue(Str::startsWith('test', ['t', 's']));
        $this->assertFalse(Str::startsWith('test', ['a', 's']));
    }

    public function testEndsWithElementInArray()
    {
        $this->assertTrue(Str::endsWith('test', ['st', 'ts']));
    }
}
