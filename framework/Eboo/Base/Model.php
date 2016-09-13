<?php

namespace Eboo\Base;

class Model
{
    protected $table;
    private static $instance;
    private $app;

    private $columns = [];
    public $values;
    private static $column_array;
    public $primary_key = [];
    private $isNew = true;
    public $errors = [];

    public function __construct($id=null)
    {
        $this->app = \Eboo\Factory\AppFactory::getApp();

        $this->values = new \stdClass();
        $this->columns = $this->getColumns();

        if (isset($id)) {
            $this->isNew = false;
            $this->setPrimaryKey($id);
            $this->getValues();
        }
    }

    private function setPrimaryKey($id)
    {
        if (is_array($id)) {
            foreach ($id as $key => $val) {
                $this->primary_key[$key] = $val;
            }
        } else {
            foreach ($this->primary_key as $key => $val) {
                $this->primary_key[$key] = $id;
            }
        }
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getColumns()
    {
        if(count($this->columns) == 0) {
            $result = $this->app->getDatabase()->getTableColumns($this->table);
            foreach ($result as $column) {
                $this->values->$column['Field'] = null;
                if ($column['Key'] == 'PRI') {
                    $this->primary_key[$column['Field']] = null;
                }
            }
        }
    }

    public function getValues()
    {
        $result = $this->app->getDatabase()->selectQuery($this->primary_key);
        if (!$result) {
            throw new \Exception('Error running sql query');
        }
        foreach ($result as $key => $val) {
            $this->values->$key = $val;
        }
        return $result;
    }

    public function setValues(array $in_array)
    {
        foreach ($this->values as $key => $val) {
            if (isset($in_array[$key])) {
                $this->values->$key = $in_array[$key];
            }
        }
    }

    public function delete()
    {
        $query_builder = $this->app->getDatabase()->findQuery($this->primary_key);
        $result = $this->app->getDatabase()->query("Delete from `{$this->table}` {$query_builder['query']} limit 1", $query_builder['values']);
        if (!$result) {
            throw new \Exception('Error deleting from the database');
        }
    }

    public function save()
    {
        if ($this->isNew) {
            return $this->insert();
        }
        return $this->update();
    }

    public function insert()
    {
        $query_builder = $this->app->getDatabase()->insertQuery($this->values,$this->table);
        $result = $this->app->getDatabase()->query($query_builder['query'], $query_builder['values']);
        if (count($this->primary_key) == 1) {
            $id = $this->app->getDatabase()->lastInsertId();
            foreach ($this->primary_key as $key => $val) {
                $this->primary_key[$key] = $id;
            }
        } else {
            foreach ($this->primary_key as $key => $val) {
                $this->primary_key[$key] = $this->values->$key;
            }
        }
        if (!$result) {
            throw new \Exception('Error adding to database.');
        }
        $this->isNew = false;
    }

    public function update()
    {
        $query_builder = $this->app->getDatabase()->updateQuery($this->primary_key,$this->values,$this->table);
        $result = $this->app->getDatabase()->query($query_builder['query'], $query_builder['values']);
        if (!$result) {
            throw new \Exception('Error updating database.');
        }
    }

    public static function find($criteria = [])
    {
        $model = self::getInstance();
        $result = $model->app->getDatabase()->selectQuery($criteria,$model->table);
        if ($result) {
            $result->isNew = false;
        }
        return $result;
    }

    public static function findAll($criteria = [])
    {
        $model = self::getInstance();
        $result = $model->app->getDatabase()->selectQuery($criteria,$model->table,false);
        if ($result) {
            foreach ($result as $res) {
                $res->isNew = false;
            }
        }
        return $result;
    }

    public function __get($name)
    {
        if (method_exists($this, $name)) {
            return null;
        }
        return $this->values->$name;
    }

    public function __set($name, $value)
    {
        $this->values->$name = $value;
    }
}