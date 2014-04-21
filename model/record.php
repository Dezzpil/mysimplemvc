<?php

namespace msmvc\model;

use msmvc\model\exception_norecord;
use msmvc\model\exception_record;
use msmvc\model\exception_query;
use msmvc\model\query_where;
use msmvc\model\query_order;
use msmvc\db;

/**
 * Provides simple abstraction for object mapping
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */

class record {
    
    const COL_TYPE_INT = 'int';
    const COL_TYPE_STRING = 'string';
    
    static protected $unicKey = 'id';
    static protected $unicType = self::COL_TYPE_INT;
    static protected $tbl_name;
    
    protected $origin_vals = array();
    protected $vals = array();
    protected $id = null;

    /**
     * @param query_where $where
     * @throws \Exception|\msmvc\model\exception_query
     */
    static function delete(query_where $where) {
        $query = "DELETE FROM ".static::$tbl_name.$where->get_prepared();
        try {
            db::instance()->query($query);
        } catch (exception_query $e) {
            throw $e;
        }
    }

    static function getUnicKey() {
        return self::$unicKey;
    }

    static protected $loadedVars = array();

    /**
     * @param $id
     * @throws exception_query
     * @throws exception_norecord
     */
    static function load($id) {
        if (static::$unicType == 'string') $id = '"'.$id.'"';
        $query = 'select * from '.static::$tbl_name.' where '.static::$unicKey.'='.$id;
        $props = db::instance()->query($query);

        if (! empty($props)) {

            self::saveQueriedToClass($props[0]);
            return new static;

        } else {
            throw new exception_norecord('No object with given id');
        }
    }

    /**
     * @var string
     */
    static protected $fields = '*';

    /**
     * Установить список полей для метода get_list()
     * @param array $list
     */
    static function set_fields(array $list) {
        self::$fields = join(',', $list);
    }

    /**
     * @param query_where $where
     * @param query_order $order
     * @param array $limit
     * @param bool $asArray
     * @return array
     * @throws exception_norecord
     */
    static function get_list(
            query_where $where = null, 
            query_order $order = null, 
            $limit = array(),
            $asArray = false
    ) {

        $query = "SELECT ".self::$fields." FROM ".static::$tbl_name;
        self::$fields = '*';
        
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

        $result = db::instance()->query($query);

        if (empty($result)) {
            throw new exception_norecord('No rows in '.static::$tbl_name.' in result by query '.$query);
        }

        $objects = array();
        foreach ($result as $row) {

            self::saveQueriedToClass($row);
            $obj = new static();
            if ($asArray) $obj = $obj->toArray();

            if (array_key_exists(static::$unicKey, $row) === false) {
                $objects[] = $obj;
            } else {
                $objects[$row[static::$unicKey]] = $obj;
            }
        }
        
        return $objects;
    }

    static function add() {
        return new static();
    }

    /**
     * Set argumented values to class context,
     * usually argument contains result from query
     *
     * @param $row
     */
    protected static function saveQueriedToClass($row) {
        foreach ($row as $key => $val) {
            self::$loadedVars[$key] = $val;
        }
    }

    /**
     * Set changed values to class context
     */
    protected function saveChangesToClass() {
        foreach ($this->vals as $key => $val) {
            self::$loadedVars[$key] = $val;
        }
    }

    /**
     * Set origin vals from class context
     */
    protected function loadValuesFromClass() {
        if ( ! empty(static::$loadedVars)) {
            foreach (static::$loadedVars as $key => $val) {
                $this->origin_vals[$key] = $val;
            }
            static::$loadedVars = array();
        }
    }

    /**
     * Get values from class context,
     * when instance object
     */
    public function __construct() {
        $this->loadValuesFromClass();
    }

    function __set($key, $value) {
        $this->set($key, $value);
    }
    
    function __get($key) {
        return $this->get($key);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    function set($key, $value) {

        $this->vals[$key] = $value;

        if (empty($this->origin_vals[$key])) {
            $this->origin_vals[$key] = @$this->vals[$key];
        }

        return $this;
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function getOrigin($key) {
        return @$this->origin_vals[$key];
    }

    /**
     * @param $key
     * @return mixed
     */
    function get($key) {

        $val = arr::get($key, $this->vals);
        if ( ! $val) $val = $this->getOrigin($key);

        return $val;
    }


    /**
     * @return string
     * @throws exception_record
     */
    function get_id() {
        
        $idKey = static::$unicKey;
        $id = arr::get('id', $this->origin_vals);

        if ( ! empty($id) && $id !== null) {
            return $id;
        }

        throw new exception_record(
            'Id ('.$idKey.') for essence '.static::$tbl_name.' undefined'
        );
    }

    /**
     * @param $val
     */
    protected function setId($val) {
        self::$loadedVars[static::$unicKey] = $val;
        $this->id = $val;
        $this->loadValuesFromClass();
    }

    /**
     * @return array|int|string
     */
    function remove() {
        $query = "DELETE FROM ".static::$tbl_name." where `".self::getUnicKey()."`=".$this->get_id();
        $result = db::instance()->query($query);
        return $result;
    }

    /**
     * 
     * @throws exception_query
     */
    function save() {

        $this->beforeSave();

        try {

            $this->get_id();
            $this->update();

        } catch (\Exception $e) {

            $userId = $this->insert();
            $this->setId($userId);

        }

        $this->saveChangesToClass();
        $this->loadValuesFromClass();
        $this->afterSave();

        return $this;
    }

    /**
     * @return array|int|string
     */
    protected function update() {
        $idKey = static::$unicKey;
        $query = "UPDATE ".static::$tbl_name." SET ";
        foreach ($this->vals as $prop => $value) {
            $query .= $prop."=";
            $value = db::prepare_string($value);
            if (is_string($value)) $query .= "'".$value."'";
            elseif (is_int($value)) $query .= $value;
            elseif (is_float($value)) $query .= $value;
            else $query .= "'".$value."'";
            $query .= ", ";
        }

        $query = substr($query, 0, strlen($query) - 2);
        $query .= " WHERE ".$idKey."=".$this->get_id();
        $result = db::instance()->query($query);

        return $result;
    }
    
    protected function insert() {
        $query = "insert into ".static::$tbl_name." (".join(",", array_keys($this->vals)).") values (";
        foreach ($this->vals as $value) {
            $value = db::prepare_string($value);
            if (is_string($value)) $query .= "'".$value."'";
            elseif (is_int($value)) $query .= $value;
            elseif (is_float($value)) $query .= $value;
            else $query .= "'".$value."'";
            $query .= ", ";
        }
        $query = substr($query, 0, strlen($query) - 2);
        $query .= ")";
        
        $result = db::instance()->query($query);


        return $result;
    }

    /**
     * @return array
     */
    function toArray() {
        $array = array();
        $vals = array_merge($this->origin_vals, $this->vals);

        foreach ($vals as $key => $val) {
            if (is_object($val)) $val = $val->toArray();
            $array[$key] = $val;
        }
        
        return $array;
    }

    /**
     * hooks mechanism
     */

    const HOOK_BEFORE_SAVE = 1;
    const HOOK_AFTER_SAVE = 2;

    protected $hookStorage = array(
        1 => array(),
        2 => array()
    );

    /**
     * @param int $hookInt
     * @param function $fn
     * @param array $argv
     */
    protected function addHook($hookInt, $fn, $argv = array()) {
        array_push(
            $this->hookStorage[$hookInt],
            array('fn' => $fn, 'argv' => $argv)
        );
    }

    /**
     * @param int $hookInt
     */
    protected function clearHooks($hookInt) {
        $this->hookStorage[$hookInt] = array();
    }

    /**
     * @param int $hookInt
     */
    protected function execHook($hookInt) {
        if ( ! empty($this->hookStorage[$hookInt]))
            foreach ($this->hookStorage[$hookInt] as $hook) {
                $argv = array_merge(array('me' => $this), $hook['argv']);
                //$argv = $hook['argv'];
                call_user_func_array($hook['fn'], $argv);
            }
    }

    /**
     * hooks bindings
     */

    protected function beforeSave() {
        $this->execHook(self::HOOK_BEFORE_SAVE);
    }

    protected function afterSave() {
        $this->execHook(self::HOOK_AFTER_SAVE);
    }
    
}

?>