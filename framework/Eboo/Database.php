<?php

namespace Eboo;

use PDO;

class Database
{
    /**
     * @var array
     */
    protected $credentials;

    /**
     * @var PDO
     */
    protected $connection;

    public function __construct($credentials)
    {
        $this->credentials = $credentials;
        $this->connect();
    }

    public function connect()
    {
        if(!isset($this->connection)) {
            try {
                $this->connection = new PDO(
                    "mysql:host={$this->credentials['host']};dbname={$this->credentials['database']}",
                    $this->credentials['user'],
                    $this->credentials['password'],
                    array(PDO::ATTR_PERSISTENT => true)
                );
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            } catch (PDOException $pe) {
                die('Connection error, because: ' . $pe->getMessage());
            }
        }
    }

    public function query($query, $values = [], $options = [])
    {
        $sql_query = $this->connection->prepare($query);
        if (!$sql_query) {
            throw new \Exception('Error preparing query');
        }

        $sql_query->setFetchMode(PDO::FETCH_ASSOC);
        if (isset($options['class'])) {
            $sql_query->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $options['class']);
        }

        foreach ($values as $key => $val) {
            $sql_query->bindValue($key, $val);
        }
        $sql_query->execute();
        if ($sql_query->errorCode() > 0) {
            throw new \Exception('Error running query. ' . $sql_query->errorInfo());
        }
        return $sql_query;
    }

    public function find($query, $values = [], $options = [])
    {
        return $this->query($query, $values, $options);
    }

    public function fetchAll($query, $values = [], $options = [])
    {
        $sql_query = $this->query($query, $values, $options);
        return $sql_query->fetchAll();
    }

    public function fetch($query, $values = [], $options = [])
    {
        $sql_query = $this->query($query, $values, $options);
        return $sql_query->fetch();
    }

    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    public function getTableColumns($table)
    {
        return $this->connection->fetchAll("SHOW COLUMNS FROM `{$table}`");
    }

    public function findQuery($criteria = [])
    {
        $query_array = [];
        $val_array = [];
        $query_string = '';
        foreach ($criteria as $key => $val) {
            $query_array[] = "`$key`=:$key";
            $val_array[":$key"] = $val;
        }
        if (count($query_array) > 0) {
            $query_string = 'where ' . implode(' and ', $query_array);
        }

        return ['query' => $query_string, 'values' => $val_array];
    }

    public function insertQuery($values,$table)
    {
        $key_array = [];
        $values_array = [];
        $val_array = [];
        foreach ($values as $key => $val) {
            if ($key != 'id') {
                $key_array[] = $key;
                $values_array[] = ":$key";
                $val_array[":$key"] = $val;
            }
        }
        $query_string = implode(',', $values_array);
        $key_string = implode(',', $key_array);
        return [
            'query' => "insert into `{$table}` ($key_string) values ($query_string)",
            'values' => $val_array,
        ];
    }

    public function updateQuery($primary,$values,$table)
    {
        $query_builder = $this->findQuery($primary);
        $query_array = [];
        $val_array = $query_builder['values'];
        foreach ($values as $key => $val) {
            $query_array[] = "`$key`=:set_$key";
            $val_array[":set_$key"] = $val;
        }
        $query_string = implode(', ', $query_array);
        return [
            'query' => "Update `{$table}` set $query_string {$query_builder['query']}",
            'values' => $val_array,
        ];
    }

    public function selectQuery($criteria = [],$table,$single=true)
    {
        $query_builder = $this->findQuery($criteria);
        $limit = "";
        $method = 'fetchAll';
        if($single) {
            $limit = " limit 1 ";
            $method = 'fetch';
        }
        return $this->$method("SELECT * from `{$table}` {$query_builder['query']} {$limit}", $query_builder['values'], array('class' => $table));
    }
}