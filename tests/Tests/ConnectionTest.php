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
use Spw\Connection\Connection;


class ConnectionTest extends PHPUnit_Framework_TestCase
{
    public function testSelectOrderBy()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->withCache()
        ->from('books')
            ->orderBy('name')
            ->select();

        $expected = [
            0 =>
                [
                    'id' => 3,
                    'name' => 'Thinking in Python',
                    'tags' => '["python", "development"]',
                ],
            1 =>
                [
                    'id' => 2,
                    'name' => 'Core Java',
                    'tags' => '["java", "development"]',
                ],
            2 =>
                [
                    'id' => 1,
                    'name' => 'Code Complete',
                    'tags' => '["development", "software engineering"]',
                ],
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testSelect()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->from('books')
            ->select();

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

    public function testSelectOne()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->from('books')
            ->selectOne();
        $expected = [
            0 =>
                [
                    'id' => 1,
                    'name' => 'Code Complete',
                    'tags' => '["development", "software engineering"]',
                ],
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testSelectMultiOrderBy()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->from('books')
            ->orderBy('name', 'asc')
            ->orderBy('id')
            ->select(['tags']);

        $expected = [
            0 =>
                [
                    'tags' => '["development", "software engineering"]',
                ],
            1 =>
                [
                    'tags' => '["java", "development"]',
                ],
            2 =>
                [
                    'tags' => '["python", "development"]',
                ],
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testSelectOrderByLimitString()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->from('books')
            ->orderBy('name')
            ->limit('2')
            ->select('tags');

        $expected = [
            0 =>
                [
                    'tags' => '["python", "development"]',
                ],
            1 =>
                [
                    'tags' => '["java", "development"]',
                ],
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testSelectOrderByLimit()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->from('books')
            ->orderBy('name')
            ->limit(2)
            ->select('tags');

        $expected = [
            0 =>
                [
                    'tags' => '["python", "development"]',
                ],
            1 =>
                [
                    'tags' => '["java", "development"]',
                ],
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testWhereEquals()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->from('books')
            ->where([
                'name' => 'Code Complete',
            ])
            ->select();

        $expected = [
            0 =>
                [
                    'id' => 1,
                    'name' => 'Code Complete',
                    'tags' => '["development", "software engineering"]',
                ],
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testMultiWhere()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->from('books')
            ->where([
                'id' => 1,
                'name' => 'Code Complete',
            ])
            ->select();

        $expected = array(
            0 =>
                array(
                    'id' => 1,
                    'name' => 'Code Complete',
                    'tags' => '["development", "software engineering"]',
                ),
        );

        $this->assertEquals($expected, $actual);
    }

    public function testWhereIn()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->from('books')
            ->where([
                'id' => ['IN', [1, 2]],
            ])
            ->select();

        $expected = array(
            0 =>
                array(
                    'id' => 1,
                    'name' => 'Code Complete',
                    'tags' => '["development", "software engineering"]',
                ),
            1 =>
                array(
                    'id' => 2,
                    'name' => 'Core Java',
                    'tags' => '["java", "development"]',
                ),
        );

        $this->assertEquals($expected, $actual);
    }

    public function testWhereGreaterThan()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->from('books')
            ->where([
                'id' => ['>', 1],
            ])
            ->select();

        $expected = [
            0 =>
                [
                    'id' => 2,
                    'name' => 'Core Java',
                    'tags' => '["java", "development"]',
                ],
            1 =>
                [
                    'id' => 3,
                    'name' => 'Thinking in Python',
                    'tags' => '["python", "development"]',
                ],
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testWhereJsonContains()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->from('books')
            ->where([
                'tags' => ['JSON_CONTAINS', 'python'],
            ])
            ->select();

        $expected = array(
            0 =>
                array(
                    'id' => 3,
                    'name' => 'Thinking in Python',
                    'tags' => '["python", "development"]',
                ),
        );

        $this->assertEquals($expected, $actual);
    }

    public function testWhereJsonValueEquals()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->from('books')
            ->where([
                'tags.1' => 'development'
            ])
            ->select();

        $expected = [
            0 =>
                [
                    'id' => 2,
                    'name' => 'Core Java',
                    'tags' => '["java", "development"]',
                ],
            1 =>
                [
                    'id' => 3,
                    'name' => 'Thinking in Python',
                    'tags' => '["python", "development"]',
                ],
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testWhereJsonNamedEquals1()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->from('pairs')
            ->where([
                'dogs.foo' => 'bar',
            ])
            ->select();

        $expected = [
            0 =>
                [
                    'id' => 2,
                    'name' => 'wangqingchun',
                    'dogs' => '{"foo": "bar", "microsoft": "bing"}',
                ],
            1 =>
                [
                    'id' => 3,
                    'name' => 'wangqingchun',
                    'dogs' => '{"bar": 3, "foo": "bar", "microsoft": "bing"}',
                ],
            2 =>
                [
                    'id' => 4,
                    'name' => 'wangqingchun',
                    'dogs' => '{"bar": 9, "foo": "bar", "microsoft": "bing"}',
                ],
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testWhereJsonNamedEquals2()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->from('pairs')
            ->where([
                'dogs.microsoft' => 'bing',
            ])
            ->select();

        $expected = [
            0 =>
                [
                    'id' => 2,
                    'name' => 'wangqingchun',
                    'dogs' => '{"foo": "bar", "microsoft": "bing"}',
                ],
            1 =>
                [
                    'id' => 3,
                    'name' => 'wangqingchun',
                    'dogs' => '{"bar": 3, "foo": "bar", "microsoft": "bing"}',
                ],
            2 =>
                [
                    'id' => 4,
                    'name' => 'wangqingchun',
                    'dogs' => '{"bar": 9, "foo": "bar", "microsoft": "bing"}',
                ],
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testWhereJsonNamedEqualsMixed()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->from('pairs')
            ->where([
                'dogs.foo' => 'bar',
                'id' => ['>', 1],
            ])
            ->select();

        $expected = [
            0 =>
                [
                    'id' => 2,
                    'name' => 'wangqingchun',
                    'dogs' => '{"foo": "bar", "microsoft": "bing"}',
                ],
            1 =>
                [
                    'id' => 3,
                    'name' => 'wangqingchun',
                    'dogs' => '{"bar": 3, "foo": "bar", "microsoft": "bing"}',
                ],
            2 =>
                [
                    'id' => 4,
                    'name' => 'wangqingchun',
                    'dogs' => '{"bar": 9, "foo": "bar", "microsoft": "bing"}',
                ],
        ];

        $this->assertEquals($expected, $actual);
    }


    public function testWhereJsonNamedGreaterThan()
    {
        $conn = new Connection(new DevConfig());
        $actual = $conn->from('pairs')
            ->where([
                'dogs.bar' => ['<', 9],
            ])
            ->select();

        $expected = [
            0 =>
                [
                    'id' => 3,
                    'name' => 'wangqingchun',
                    'dogs' => '{"bar": 3, "foo": "bar", "microsoft": "bing"}',
                ],
        ];

        $this->assertEquals($expected, $actual);
    }
}
