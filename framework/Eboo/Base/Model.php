<?php

namespace Eboo\Base;

class Model
{
    protected $table;
    protected $database;
    protected $isNew = true;
    protected $values;
    protected $primary_key = [];
    protected $errors;

    protected static $column_array;

    public $called_class;
    public $columns;

    public function __construct($id=null)
    {
        $this->database = \Eboo\Factory\DatabaseFactory::getDatabase();

        $this->called_class = get_called_class();
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
        $class = get_called_class();
        return new $class();
    }

    public function getColumns()
    {
        $class = $this->called_class;
        if(empty($class::$column_array)) {
            $result = $this->database->getTableColumns($this->table);
            foreach ($result as $column) {
                $fieldName = $column['Field'];
                $columns[] = $fieldName;
                $this->values->$fieldName = null;
                if ($column['Key'] == 'PRI') {
                    $this->primary_key[$fieldName] = null;
                }
            }
            $class::$column_array = $columns;
        }
        return $class::$column_array;
    }

    public function getValues()
    {
        $result = $this->database->selectQuery($this->primary_key);
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
        $query_builder = $this->database->findQuery($this->primary_key);
        $result = $this->database->query("Delete from `{$this->table}` {$query_builder['query']} limit 1", $query_builder['values']);
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
        $query_builder = $this->database->insertQuery($this->values,$this->table);
        $result = $this->database->query($query_builder['query'], $query_builder['values']);
        if (count($this->primary_key) == 1) {
            $id = $this->database->lastInsertId();
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
        $query_builder = $this->database->updateQuery($this->primary_key,$this->values,$this->table);
        $result = $this->database->query($query_builder['query'], $query_builder['values']);
        if (!$result) {
            throw new \Exception('Error updating database.');
        }
    }

    public function getTable()
    {
        return $this->table;
    }

    public static function find($criteria = [])
    {
        $model = self::getInstance();
        $result = $model->database->selectQuery($criteria,$model->getTable(),get_called_class());
        if ($result) {
            $result->isNew = false;
            $result->updatePrimary();
        }
        return $result;
    }

    public static function findAll($criteria = [])
    {
        $model = self::getInstance();
        $result = $model->database->selectQuery($criteria,$model->getTable(),get_called_class(),false);
        if ($result) {
            foreach ($result as $res) {
                $res->isNew = false;
                $res->updatePrimary();
            }
        }
        return $result;
    }

    public function updatePrimary()
    {
        foreach($this->primary_key as $key => $val) {
            $this->primary_key[$key] = $this->values->$key;
        }
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