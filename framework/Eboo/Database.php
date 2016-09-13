<?php

namespace Eboo;

class Database
{
    protected $credentials;

    public function __construct($credentials)
    {
        $this->credentials = $credentials;
    }
}