<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/17/16
 * Time: 7:10 PM
 */

namespace Spw\Support;


/**
 * Time related operations.
 *
 * @package Spw\Support
 */
class Time
{
    /**
     * Current timestamp in millisecond.
     *
     * @return mixed
     */
    public static function millisecond()
    {
        return microtime(true);
    }
}