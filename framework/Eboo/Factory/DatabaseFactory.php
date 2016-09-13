<?php

namespace Eboo\Factory;

use Eboo\Database;

class DatabaseFactory
{
    private static $database;

    public static function getDatabase($config = [])
    {
        if (!self::$database)
            self::$database = new Database($config);
        return self::$database;
    }
}