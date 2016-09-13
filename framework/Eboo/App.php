<?php

namespace Eboo;

use Eboo\Factory\DatabaseFactory;
use Eboo\Factory\RequestFactory;
use Eboo\Factory\RouterFactory;

class App
{
    protected $config;
    protected $router;
    protected $database;
    protected $request;

    public function __construct($config)
    {
        $this->config = $config;
        $this->router = RouterFactory::getRouter($this->config['routes']);
        $this->database = DatabaseFactory::getDatabase($this->config['db']);
        $this->request = RequestFactory::getRequest();
    }

    public function run()
    {
        try{
            $response = $this->getRoute($this->router,$this->request);
        } catch (\Exception $e) {
            $response = new Response("Error: {$e->getMessage()}<br><br>File: {$e->getFile()}<br><br>Line: {$e->getLine()}");
        }
        $this->handleResponse($response);
    }

    public function getRoute(Router $router, Request $request)
    {
        $response = $router->getRoute($request);
        if(!$response['action']) {
            return new Response('Page Not Found',404);
        }
        if(is_callable($response['action'])) {
            return $response['action']($response['variables']);
        } else {
            return $this->callController($response['action'],$response['variables'],$request);
        }
    }

    private function handleResponse($response)
    {
        if(is_object($response)) {
            $response->html();
        } else {
            if(is_string($response)) {
                $response = new Response($response,200);
                $response->html();
            } else {
                echo "Not sure what to do with this response type";
                ddd($response);
            }
        }
    }

    private function callController($action,$variables,Request $request)
    {
        list($controller,$function) = explode('->',$action);
        $namespacedController = "app\\Controllers\\{$controller}";
        $reflected = new \ReflectionClass($namespacedController);
        $arguments = $reflected->getMethod($function)->getParameters();

        $passArguements = $this->getArguments($arguments,$variables,$request);
        return call_user_func_array([new $namespacedController($this),$function],$passArguements);
    }

    private function getArguments($arguments,$variables,Request $request)
    {
        $passArguements = [];
        foreach($arguments as $arguement) {
            if($arguement->getClass() && $arguement->getClass()->getName() == 'Eboo\Request') {
                $passArguements[] = $request;
            } else {
                if(array_key_exists($arguement->name,$variables)) {
                    $passArguements[] = $variables[$arguement->name];
                } else {
                    if(!$arguement->isOptional()) {
                        throw new \Exception("{$arguement->name} must be passed in to the function");
                    }
                }
            }
        }
        return $passArguements;
    }

    public function getConfig($setting)
    {
        $pieces = explode('.',$setting);
        $name = array_shift($pieces);
        $variable = $this->config[$name];
        while(count($pieces) > 0) {
            $name = array_shift($pieces);
            $variable = $variable[$name];
        }

        return $variable;
    }

    public function getDatabase()
    {
        return $this->database;
    }
}