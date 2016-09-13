<?php

namespace Eboo\Base;

class Model
{
    protected $table;
    private static $instance;

    public function __construct()
    {
        echo "_connstruct called <br>";
    }

    public static function findById($id)
    {
        $model = self::getInstance();
        return $model->query(['id'=>$id]);
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query($values)
    {
        return $values;
    }
}