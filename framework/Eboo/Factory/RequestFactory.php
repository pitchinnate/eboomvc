<?php

namespace Eboo\Factory;

use Eboo\Request;

class RequestFactory
{
    private static $request;

    public static function getRequest()
    {
        if (!self::$request)
            self::$request = new Request();
        return self::$request;
    }
}