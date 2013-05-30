<?php
namespace sql;
use help;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of record
 *
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class query_record {
    //put your code here
    
    static protected $tbl_name = null;
    
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
     * @return array \static
     */
    static function get_list(
            query_where $where = null, 
            query_order $order = null, 
            array $limit = array()
    ) {
        $query = "SELECT * FROM ".static::$tbl_name;
        
        if ($where !== null) {
            $query .= $where->get_prepared();
        }
        
        if ($order != null) {
            $query .= $order->get_prepared();
        }
        
        switch (count($limit)) {
            case 1 : 
                $query .= ' LIMIT '.$limit[0]; break;
            case 2 :
                $query .= ' LIMIT '.$limit[0].','.$limit[1]; break;
        }
        
        $result = \db::instance()->query($query);
        $objects = array();
        foreach ($result as $row) {
            $objects[$row['id']] = new static();
            foreach ($row as $key => $val) {
                if (NEED_TO_CONVERT_UTF8) {
                    $objects[$row['id']]->$key = \help\xhelp::win1251_to_utf8($val);
                } else {
                    $objects[$row['id']]->$key = $val;
                }
            }
        }
        
        return $objects;
    }
    
    public function __construct($id = null) {    
        if (intval($id)) { 
            $this->load($id);     
        }
    }
    
    protected function load($id) {
        $query = "select * from ".static::$tbl_name." where id=$id";
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
                $value = help\xhelp::utf8_to_win1251($value);
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
     * @throws \Exception
     */
    function get_id() {
        if ($this->id) {
            return $this->id;
        }
        
        throw new \Exception('Id для объекта сущности '.static::$tbl_name.' неопределен');
    }

    function save() {
        try {
            if (intval($this->id) > 0) {
                $this->update();
            } else {
                $this->id = $this->insert();
            }
        } 
        catch (query_exception $e) {
            die($e->GetCode().' '.$e->GetMessage());
        }
    }
    
    protected function update() {
        
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
        $query .= " WHERE id=".$this->id;

        return \db::instance()->query($query);
    }
    
    protected function insert() {
        $query = "INSERT INTO ".static::$tbl_name." (".join(",", array_keys($this->loaded_vals)).") VALUES (";
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
        
        $result = \db::instance()->query($query);
        
        return $result;
    }
}

?>
