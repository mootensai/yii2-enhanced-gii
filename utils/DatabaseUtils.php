<?php
/**
 * Created by PhpStorm.
 * User: gogl92
 * Date: 2/1/19
 * Time: 11:30 AM
 */

namespace inquid\enhancedgii\utils;


class DatabaseUtils
{
    public static function getDsnAttribute($dsn, $name = 'dbname')
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }
}