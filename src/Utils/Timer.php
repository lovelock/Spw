<?php

/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/17/16
 * Time: 7:17 PM
 */
namespace Spw\Utils;

use Spw\Support\Time;

/**
 * Timer for performance test.
 *
 * @package Spw\Utils
 */
class Timer
{
    private static $container;

    /**
     * Start a timer for a specified service.
     *
     * @param $name
     */
    public static function start($name)
    {
        self::$container[$name]['start'][] = Time::millisecond();
    }

    /**
     * Stop the timer for a specified service.
     *
     * @param $name
     */
    public static function stop($name)
    {
        self::$container[$name]['end'][] = Time::millisecond();
    }

    /**
     * Sum up all the time the service consumed.
     *
     * @param $name
     * @return int
     */
    public static function get($name)
    {
        $time = 0;
        if (array_key_exists($name, self::$container)
            && isset(self::$container[$name]['start'], self::$container[$name]['stop'])
            && ($count = count(self::$container[$name]['start']) === count(self::$container[$name]['stop']))
        ) {
            for ($i = 0; $i < $count; $i++) {
                $time += self::$container[$name]['stop'][$i] - self::$container[$name]['start'][$i];
            }
        }

        return $time;
    }
}