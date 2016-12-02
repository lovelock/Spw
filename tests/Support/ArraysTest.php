<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 12/2/16
 * Time: 3:29 PM
 */

namespace Tests\Spw\Support;


use Spw\Support\Arrays;


class ArraysTest extends \PHPUnit_Framework_TestCase
{
    public function testIsNestedArray()
    {
        $nestedArray = [
            [
                1,
                2,
            ],
            [
                2,
                4,
            ]
        ];

        $this->assertTrue(Arrays::isNested($nestedArray));
    }

    public function testIsNestedAssocArray()
    {
        $assocArray = [
            'foo' => [
                1,
                2,
            ],
            'bar' => [
                2,
                4,
            ],
        ];

        $this->assertTrue(Arrays::isNested($assocArray));
    }

    public function testNotNestedArray()
    {
        $notNested = [
            1, 2, 3,
        ];

        $this->assertFalse(Arrays::isNested($notNested));
    }

    public function testNotAssocNested()
    {
        $notNestedAssoc = [
            'foo' => 'bar',
            'google' => 'search',
        ];

        $this->assertFalse(Arrays::isNested($notNestedAssoc));
    }
}
