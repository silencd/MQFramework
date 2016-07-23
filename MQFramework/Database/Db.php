<?php
namespace MQFramework\Database;

use MQFramework\Database\Connector;
use MQFramework\Database\Exceptions\DBException;

class Db
{
    private $handle = null;
    private $wheres = null;
    private $column_data = null;
    private $table = null;
    private $table_prefix = null;
    private $column = '*';
    private $limit = null;
    private $order = null;
    private $sql = null;
    private $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', 'between',
    ];

    public function __construct()
    {
        $this->handle = $this->getInstance();
    }
    public function connect()
    {
        $conn = new Connector();
        try {
            if (isset( $conn->config['table_prefix'] ) && !empty($conn->config['table_prefix'])) {
                $this->table_prefix = $conn->config['table_prefix'];
            }
            $this->handle = new \PDO(
                $conn->config['dsn'], $conn->config['username'], $conn->config['password']
            );
            $this->handle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new DBException($e->getMessage());
        }
    }
    public function getInstance()
    {
        if ($this->handle === null) {
            $this->connect();
        }
        return $this->handle;
    }
    public function select($params)
    {
        $this->column = isset($params) ? $params : $this->column;
        if ( is_array( $this->column ) ) {
            //array to strings
            $this->column = implode(",", $params);
        } else {
            $this->column = (string) $params;
        }
        return $this;
    }
    public function update()
    {
        $this->build('update');
        if ( $this->execute($this->prepare()) ) {
            return true;
        }
    }
    public function delete()
    {
        $this->build('delete');
        if ( $this->execute($this->prepare()) ) {
            return true;
        }
    }
    public function save(array $params)
    {
        $columns = $columnValue = [];
        foreach ( $params as $columnName => $value ) {
            if ( is_numeric($columnName) ) {
                throw new \Exception("<br>Column Name is Integer!");
            }
            $columns[] = '`'.$columnName.'`';
            $columnValue[] = "'".$value."'";
        }
        $data = [
            'column' => implode(',', $columns),
            'value' => implode(',', $columnValue),
        ];
        $this->build('insert', ['save' => $data]);
        if ( $this->execute($this->prepare()) ) {
            try {
                return $this->handle->lastInsertId();
            } catch (\Exception $e) {
                echo "<br>Error in get last insert id : ".$e->getMessage();
            }
            return true;
        }
    }
    public function get()
    {
        $this->build('select');
        $obj = $this->prepare();
        if ( $this->execute($obj) ) {
            $data = [];
            foreach ($obj->fetchAll() as $key => $value) {
                $data[$key] = $value;
            }
            // 返回一维数组
            if (count($data) == 1) {
                return $data[0];
            }
            return $data;
        }
    }
    /**
     * [where description "id > 2" or ['id', '=', ''3]]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function where($params)
    {
        if ( is_string($params) ) {
            $this->wheres = $params;
        }
        if ( is_array($params) ) {
            if ( count($params) === 3 && in_array(strtolower($params[1]), $this->operators, true)) {
                $this->wheres = $params[0].' '.$params[1].' \''.$params[2].'\'';
            } else {
                $condition = '';
                foreach ($params as $column => $value) {
                    if ( is_array($value) ) {
                        throw new \Exception("The Express is Not Support, ".json_encode($params));
                    }
                    $condition .= $column." = '".$value."' and ";
                }
                if ( ! empty($condition) ) {
                    $this->wheres = substr($condition, 0, -4);
                }
            }
        }
        if ( empty($this->wheres) ) {
            throw new \Exception("The Express is Not Support, ".json_encode($params));
        }
        return $this;
    }
    /**
     * [order description]
     *  (string)param is column or (array) $param[0]=column $param[1]='desc' or 'asc'
     * @param  [type]
     * @return [type]        [description]
     */
    public function order($param)
    {
        if ( is_string($param) ) {
             $this->order = " order by ".$param.' desc'; //default desc
        }
        if ( is_array($param) ) {
            if ( empty( $param[1] ) ) {
                $param[1] = 'desc';
            }
            if ( is_string( $param[0] ) && ($param[1] == 'desc' || $param[1] == 'asc') ) {
                $this->order = "order by ".$param[0].' '.$param[1];
            }
        }
        return $this;
    }
    public function limit($param)
    {
        if ( count($param) === 1 && is_int($param)) {
            $limitValue = $param;
        }
        if ( is_array($param) && is_int($param[0]) && is_int($param[1]) ) {
            if ( $param[0] === $param[1] ) {
                $limitValue = $param[0];
            } else {
                $limitValue = ($param[0] < $param[1]) ? $param[0].','.$param[1] : $param[1].','.$param[0];
            }
        }
        $this->limit = " limit ". $limitValue;
        return $this;
    }
    public function table($table = '')
    {
        if ( is_null($table) ) {
            $table = $this->table;
        }
        if ( $this->table_prefix === null ) {
            $this->table = $table;
        }
        $this->table = $this->table_prefix.$table;
        return $this;
    }
    public function data(array $params)
    {
        $newData = [];
        foreach ( $params as $column => $param ) {
            if ( is_numeric($column) ) {
                throw new \Exception("Column Name is Integer!");
            }
            $newData[] = $column."='".$param."'";
        }
        $this->column_data = implode(',', $newData);
        return $this;
    }
    public function build($action, $mix = '')
    {
        if ( $this->table == null ) {
            throw new \Exception("Database Table is not set !");
        }

        if ( $action === 'select' ) {
            $columns = ($this->column === null) ? ' * ' : $this->column;
            if ( $this->wheres === null ) {
                $sql =  "select ".$columns." from ".$this->table;
            } else {
                $sql = "select ".$columns." from ".$this->table." where ".$this->wheres;
            }
            if ( $this->order !== null) {
                $sql = $sql.$this->order;
            }
            if ( $this->limit !== null) {
                $sql = $sql.$this->limit;
            }
        }
        if ( $action === 'delete' ) {
            if ( $this->wheres === null ) {
                throw new \Exception("Can't Delete Data Without Condition !");
            }
            $sql = "delete from ".$this->table." where ".$this->wheres;
        }
        if ( $action === 'update' ) {
            if ( $this->wheres === null ) {
                throw new \Exception("Can't Update Data Without Condition !");
            }
            if ( $this->column_data === null ) {
                throw new \Exception("Column Data is Null !");
            }
            $sql = 'update '.$this->table.' set '.$this->column_data.' where '.$this->wheres;
        }
        if ( $action === 'insert' ) {
            if ( !empty($mix['save']['column']) && !empty($mix['save']['value']) ) {
                $sql = 'insert into '.$this->table.'('.$mix['save']['column'].')values('.$mix['save']['value'].')';
            }
        }
        $this->sql = $sql;
    }
    public function catchSql()
    {
        print_r($this->sql);
        return $this;
    }
    public function prepare()
    {
        if (! is_null($this->sql) ) {
            try {
                return $this->handle->prepare($this->sql);
            } catch (\Exception $e) {
                echo " Error in prepare statement({$this->sql}) : ".$e->getMessage();
            }
        }
    }
    private function execute($executor)
    {
        if ( is_object($executor) ) {
            try {
                return $executor->execute();
            } catch (\Exception $e) {
                echo " <br>Error in execute statement( {$this->sql} ) : ".$e->getMessage().'<br>';
            }
        }
    }
    public function __call($func, $params)
    {
        throw new \Exception("<br>{$func} Function not exists !");
    }
}
