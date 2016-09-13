<?php

namespace Eboo;

class App
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function run()
    {
        var_dump($this->config);
        die();
    }
}