<?php

namespace Eboo\Base;

class Model
{
    protected $table;
    private static $instance;
    private $app;

    public function __construct()
    {
        $this->app = \Eboo\Factory\AppFactory::getApp();
        ddd($this->app->getConfig('db'));
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