<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 11/16/16
 * Time: 4:57 PM
 */

namespace Spw\Support;

use InvalidArgumentException;

/**
 * String related operations.
 *
 * @package Spw\Support
 */
class Str
{
    /**
     * Check if a string starts with one of needles.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a string ends with one of needles.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if (strrpos($haystack, $needle) === strlen($haystack) - strlen($needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Quote string with specified character, default character is "'"
     *
     * @param $o
     * @param string $quote
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function quoteWith($o, $quote = '\'')
    {
        if (strlen($quote) !== 1) {
            throw new InvalidArgumentException('2nd parameter must be single character, two or more characters are given');
        }

        if (is_array($o)) {
            $len = count($o);
            for ($i = 0; $i < $len; $i++) {
                $tmp[$i] = $quote . $o[$i] . $quote;
            }

            return $tmp;
        }

        return $quote . $o . $quote;
    }
}