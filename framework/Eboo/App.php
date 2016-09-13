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
        try{
            $response = $this->getRoute($this->router,$this->request);
        } catch (\Exception $e) {
            $response = new Response("Error: {$e->getMessage()}<br><br>File: {$e->getFile()}<br><br>Line: {$e->getLine()}");
        }
        $response->html();
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

    private function callController($action,$variables,Request $request)
    {
        list($controller,$function) = explode('->',$action);
        $namespacedController = "app\\Controllers\\{$controller}";
        $reflected = new \ReflectionClass($namespacedController);
        $arguments = $reflected->getMethod($function)->getParameters();

        $passArguements = $this->getArguements($arguments,$variables,$request);
        $response = call_user_func_array([new $namespacedController(),$function],$passArguements);
        ddd($response);
    }

    private function getArguements($arguments,$variables,Request $request)
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
}