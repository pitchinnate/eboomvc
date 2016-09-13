<?php

namespace Eboo\Factory;

use Eboo\Response;

class ResponseFactory
{
    private static $response;

    public static function getResponse()
    {
        if (!self::$response)
            self::$response = new Response();
        return self::$response;
    }
}