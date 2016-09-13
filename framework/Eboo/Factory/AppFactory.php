<?php

namespace Eboo\Factory;

use Eboo\App;

class AppFactory
{
    private static $app;

    public static function getApp($config = [])
    {
        if (!self::$app)
            self::$app = new App($config);
        return self::$app;
    }
}