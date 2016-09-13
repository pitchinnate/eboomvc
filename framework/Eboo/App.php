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
        $response = $this->getRoute($this->router,$this->request);
        $response->html();
    }

    public function getRoute(Router $router, Request $request)
    {
        list($action,$variables) = $router->getRoute($request);
        if(!$action) {
            return new Response('Page Not Found',404);
        }
        if(is_callable($action)) {
            return $action($variables);
        } else {
            return $this->callController($action,$variables,$request);
        }
    }

    private function callController($action,$variables,Request $request)
    {
        list($controller,$function) = explode('->',$action);
        $namespacedController = "\\app\\Controller\\{$controller}";
        $controllerInstance = new $namespacedController();
        $reflected = new \ReflectionClass($namespacedController);
        $method = $reflected->getMethod($function);
        $arguments = $method->getParameters();
        ddd($arguments);
    }
}