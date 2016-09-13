<?php

namespace Eboo;

class App
{
    protected $config;
    protected $router;
    protected $database;
    protected $request;

    public function __construct($config)
    {
        $this->config = $config;
        $this->router = new Router($this->config['routes']);
        $this->database = new Database($this->config['db']);
        $this->request = new Request();
    }

    public function run()
    {
        $route = $this->router->getRoute();
    }
}