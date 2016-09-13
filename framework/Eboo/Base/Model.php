<?php

namespace Eboo\Base;

class Model
{
    protected $table;

    public function __construct()
    {
        echo "_connstruct called";
    }

    public static function findById($id)
    {
        return ['id' => $id];
    }

}