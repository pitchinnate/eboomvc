<?php

namespace Eboo;

class Request
{
    protected $full_url;
    protected $route;
    protected $request_type;

    public function __construct()
    {
        ddd($_SERVER);
        $this->full_url = $_SERVER[''];
    }
}