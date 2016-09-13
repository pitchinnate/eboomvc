<?php

namespace Eboo;

class Response
{
    protected $headers;
    protected $content;
    protected $code;

    public function __construct($content="",$code=200,$headers=[])
    {
        $this->content = $content;
        $this->code = $code;
        $this->headers = $headers;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function addHeader($headers)
    {
        $this->headers = array_merge($this->headers,$headers);
    }

    public function html()
    {
        http_response_code($this->code);
        foreach($this->headers as $key => $val) {
            header("{$key}: {$val}");
        }
        echo $this->content;
    }
}