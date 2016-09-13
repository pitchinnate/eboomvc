<?php

namespace Eboo;

class Router
{
    protected $routes;

    public function __construct($routes)
    {
        $this->routes = $routes;
    }
}