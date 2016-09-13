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
        $controllerInstance = new $namespacedController();
        $reflected = new \ReflectionClass($namespacedController);
        $method = $reflected->getMethod($function);
        $arguments = $method->getParameters();

        $passArguements = [];
        foreach($arguments as $arguement) {
            if($arguement->getClass()->name == 'Eboo\Request') {
                $passArguements[] = $request;
            } else {
                if(array_key_exists($arguement->name,$variables)) {
                    $passArguements[] = $variables[$arguement->name];
                } else {
                    if(!$arguement->isOptional()) {
                        return new Response("{$arguement->name} {$arguement->getClass()->name} must be passed in to the function",500);
                    }
                }
            }
        }
        $response = call_user_func_array([$controllerInstance,$function],$passArguements);
        ddd($response);
    }
}