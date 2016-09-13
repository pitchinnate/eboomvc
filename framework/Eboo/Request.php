<?php

namespace Eboo;

class Request
{
    protected $full_url;
    protected $uri;
    protected $route;
    protected $request_type;
    protected $protocol;
    protected $secure;
    protected $host;
    protected $server;
    protected $getValues;
    protected $postValues;

    public function __construct()
    {
        $this->secure = (!empty($_SERVER['HTTPS']) || $_SERVER['SERVER_PORT'] == 443);
        $this->protocol = ($this->secure) ? 'https://' : 'http://';
        $this->host = $_SERVER['HTTP_HOST'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->query = $_SERVER['QUERY_STRING'];
        $this->route = str_replace("?{$this->query}","",$this->uri);
        $this->full_url = $this->protocol . $this->host . $this->uri;
        $this->request_type = $_SERVER['REQUEST_METHOD'];
        $this->server = $_SERVER;
        $this->getValues = $_GET;
        $this->postValues = $_POST;
    }

    public function get($key,$default=null)
    {
        if(array_key_exists($key,$this->getValues)) {
            return $this->getValues[$key];
        }
        return $default;
    }

    public function post($key,$default=null)
    {
        if(array_key_exists($key,$this->postValues)) {
            return $this->postValues[$key];
        }
        return $default;
    }

    public function input($key,$default=null)
    {
        $value = $this->post($key);
        if($value) {
            return $value;
        }
        $value = $this->get($key);
        if($value) {
            return $value;
        }
        return $default;
    }

    public function getRoute()
    {
        return $this->route;
    }
}