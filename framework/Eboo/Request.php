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

    public function __construct()
    {
        $this->secure = (!empty($_SERVER['HTTPS']) || $_SERVER['SERVER_PORT'] == 443);
        $this->protocol = ($this->secure) ? 'https://' : 'http://';
        $this->host = $_SERVER['HTTP_HOST'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->query = $_SERVER['QUERY_STRING'];
        $this->route = str_replace("?{$this->query}","",$this->uri);
        $this->full_url = $this->protocol . $this->host . DIRECTORY_SEPARATOR . $this->uri;
        $this->request_type = $_SERVER['REQUEST_METHOD'];
        $this->server = $_SERVER;
    }
}