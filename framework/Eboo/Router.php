<?php

namespace Eboo;

class Router
{
    protected $routes;
    protected $action = false;
    protected $urlVariables = [];

    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    public function getRoute(Request $request)
    {
        foreach($this->routes as $route => $action) {
            if(substr($route,0,1) != '/') {
                $route = "/" . $route;
            }
            if($request->getRoute() == $route) {
                $this->action = $action;
                break;
            }
            if($this->regexRoute($route, $request->getRoute())) {
                $this->action = $action;
                break;
            }
        }
        return ['action' => $this->action, 'variables' => $this->urlVariables];
    }

    public function regexRoute($route, $requestRoute)
    {
        $piecesRoute = explode('/',$route);
        $piecesRequest = explode('/',$requestRoute);
        if(count($piecesRoute) != count($piecesRequest)) {
            return false;
        }
        $variables = [];
        foreach($piecesRoute as $key => $routePiece) {
            if(substr($routePiece,0,1) == '{' && substr($routePiece,-1) == '}') {
                $variable_name = substr($routePiece,1,-1);
                $variables[$variable_name] = $piecesRequest[$key];
            } else {
                if($routePiece != $piecesRequest[$key]) {
                    return false;
                }
            }
        }
        $this->urlVariables = $variables;
        return true;
    }
}