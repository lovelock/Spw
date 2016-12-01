<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 12/1/16
 * Time: 3:33 PM
 */

namespace Spw\Utils;


class Arrays
{
    public static function isNested(array $array)
    {
        return count(array_filter($array, 'is_array')) > 0;
    }
}