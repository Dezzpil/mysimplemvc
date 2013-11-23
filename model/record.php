<?php
namespace help;
use \sql\query_where;
use \sql\query_order;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of record
 *
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class record {
    //put your code here
    
    const COL_TYPE_INT = 'int';
    const COL_TYPE_STRING = 'string';
    
    static protected $unicKey = 'id';
    static protected $unicType = self::COL_TYPE_INT;
    static protected $tbl_name;
    
    protected $changed_vals = array();
    protected $loaded_vals = array();
    protected $id = null;
    
    static function remove($key, $cmp_opertor, $val) {
        $query = "DELETE FROM ".static::$tbl_name." where `{$key}`".$cmp_opertor.\db::prepare_string($val);
        $result = \db::instance()->query($query);
        
        return $result;
    }
    
    /**
     * Возвращает массив объектов
     * @param sql\query_where $where
     * @param sql\query_order $order
     * @param array $limit [0, 100] || [20]
     * @throws query_exception
     * @deprecated
     * @return array \static
     */
    static function get_list(
            query_where $where = null, 
            query_order $order = null, 
            $limit = array(),
            $asArray = false
    ) {
        $query = "SELECT * FROM ".static::$tbl_name;
        
        if ($where !== null) {
            $query .= $where->get_prepared();
        }
        
        if ($order != null) {
            $query .= $order->get_prepared();
        }
        
        if ($limit) {
            switch (count($limit)) {
                case 1 : 
                    $query .= ' LIMIT '.$limit[0]; break;
                case 2 :
                    $query .= ' LIMIT '.$limit[0].','.$limit[1]; break;
            }
        }
        
        $result = \db::instance()->query($query);

        $objects = array();
        foreach ($result as $row) {
            
            $obj = new static();
            
            foreach ($row as $key => $val) {
                if (NEED_TO_CONVERT_UTF8) {
                    $obj->$key = xhelp::win1251_to_utf8($val);
                } else {
                    $obj->$key = $val;
                }
            }
           
            if ($asArray) $objects[$row[static::$unicKey]] = $obj->toArray();
            else $objects[$row[static::$unicKey]] = $obj;
        }
        
        return $objects;
    }
    
    static function add() {
        return new static();
    }

    /**
     * @throws query_exception
     * @param type $id
     */
    public function __construct($id = null) {
        
        var_dump(static::$tbl_name);
        
        if ($id !== null) { 
            $this->load($id);
        }
    }
    
    /**
     * 
     * @param type $id
     * @throws query_exception
     */
    protected function load($id) {
        if (self::$unicType == 'string') $id = '"'.$id.'"';
        $query = "select * from ".static::$tbl_name." where ".static::$unicKey."=$id";
        var_dump($query);
        $props = \db::instance()->query($query);

        foreach ($props[0] as $name => $val) {
            $this->$name = $val;
        }
    }
    
    function __set($key, $value) {
        $this->loaded_vals[$key] = $value;
    }
    
    function __get($key) {
        return @$this->loaded_vals[$key];
    }
    
    function set($key, $value) {
        $this->changed_vals[$key] = @$this->loaded_vals[$key];
        
        if (NEED_TO_CONVERT_UTF8) {
            if (is_string($value)) {
                $value = xhelp::utf8_to_win1251($value);
            }
        }
        
        $this->loaded_vals[$key] = $value;
        return $this;
    }
    
    function get($key) {
        return @$this->loaded_vals[$key];
    }

     /**
     * 
     * @return int
     * @throws \record_exception
     */
    function get_id() {
        
        $idKey = static::$unicKey;
        
        if ($this->$idKey !== null) {
            return $this->$idKey;
        }
        
        throw new record_exception('Id ('.$idKey.') для объекта сущности '.static::$tbl_name.' неопределен');
    }

    /**
     * 
     * @throws query_exception
     */
    function save() {
        $idKey = static::$unicKey;
        if (intval($this->$idKey) > 0) {
            $this->update();
        } else {
            $this->$idKey = $this->insert();
        }
        
        return $this;
    }
    
    protected function update() {
        $idKey = static::$unicKey;
        $query = "UPDATE ".static::$tbl_name." SET ";
        foreach ($this->changed_vals as $prop => $value) {
            $query .= $prop."=";
            $value = \db::prepare_string($this->loaded_vals[$prop]);
            if (is_string($value)) $query .= "'".$value."'";
            elseif (is_int($value)) $query .= $value;
            elseif (is_float($value)) $query .= $value;
            else $query .= "'".$value."'";
            $query .= ", ";
        }

        $query = substr($query, 0, strlen($query) - 2);
        $query .= " WHERE ".$idKey."=".$this->$idKey;
        
        var_dump($query);

        return \db::instance()->query($query);
    }
    
    protected function insert() {
        $query = "insert into ".static::$tbl_name." (".join(",", array_keys($this->loaded_vals)).") values (";
        foreach ($this->loaded_vals as $value) {
            $value = \db::prepare_string($value);
            if (is_string($value)) $query .= "'".$value."'";
            elseif (is_int($value)) $query .= $value;
            elseif (is_float($value)) $query .= $value;
            else $query .= "'".$value."'";
            $query .= ", ";
        }
        $query = substr($query, 0, strlen($query) - 2);
        $query .= ")";
        
        var_dump(static::$tbl_name);
        echo $query;
        
        $result = \db::instance()->query($query);
        
        return $result;
    }
    
    public function toArray() {
        $array = array();
        foreach ($this->loaded_vals as $key => $val) {
            if (is_object($val)) $val = $val->toArray();
            $array[$key] = $val;
        }
        
        return $array;
    }
    
}

?>
