<?php

namespace Eboo\Factory;

use Eboo\Router;

class RouterFactory
{
    private static $router;

    public static function getRouter($config = [])
    {
        if (!self::$router)
            self::$router = new Router($config);
        return self::$router;
    }
}