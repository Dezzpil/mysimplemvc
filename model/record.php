<?php

namespace msmvc\help;

use msmvc\sql\query_exception;
use msmvc\sql\query_where;
use msmvc\sql\query_order;
use msmvc\core\db;

/**
 * Provides simple abstraction for object mapping
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 * @todo rewrite using ORM
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
     * @throws \Exception|\msmvc\sql\query_exception
     */
    static function delete(query_where $where) {
        $query = "DELETE FROM ".static::$tbl_name.$where->get_prepared();
        try {
            db::instance()->query($query);
        } catch (query_exception $e) {
            throw $e;
        }
    }

    static function getUnicKey() {
        return self::$unicKey;
    }

    static protected $loadedVars = array();

    /**
     * @param $id
     * @throws query_exception
     * @throws norecord_exception
     */
    static function load($id) {
        if (static::$unicType == 'string') $id = '"'.$id.'"';
        $query = 'select * from '.static::$tbl_name.' where '.static::$unicKey.'='.$id;
        $props = db::instance()->query($query);

        if (! empty($props)) {

            self::saveQueriedToClass($props[0]);
            return new static;

        } else {
            throw new norecord_exception('No object with given id');
        }
    }

    /**
     * @param query_where $where
     * @param query_order $order
     * @param array $limit
     * @param bool $asArray
     * @return array
     * @throws norecord_exception
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

        $result = db::instance()->query($query);

        if (empty($result)) {
            throw new norecord_exception('No rows in '.self::$tbl_name.' in result by query '.$query);
        }

        $objects = array();
        foreach ($result as $row) {

            self::saveQueriedToClass($row);
            $obj = new static();
           
            if ($asArray) $objects[$row[static::$unicKey]] = $obj->toArray();
            else $objects[$row[static::$unicKey]] = $obj;
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
        //$val = @$this->vals[$key];
        $val = arr::get($key, $this->vals);
        if ( ! $val) $val = $this->getOrigin($key);

        return $val;
    }


    /**
     * @return string
     * @deprecated
     * @throws record_exception
     */
    function get_id() {
        
        $idKey = static::$unicKey;
        //$id = @$this->origin_vals[$idKey];
        $id = arr::get('id', $this->origin_vals);

        if ( ! empty($id) && $id !== null) {
            return $id;
        }
        
        throw new record_exception(
            'Id ('.$idKey.') for essence '.static::$tbl_name.' undefined'
        );
    }

    /**
     * @return mixed
     * @throws record_exception
     */
    function getId() {
        return $this->get_id();
    }

    /**
     * @param $val
     */
    function setId($val) {
        self::$loadedVars[static::$unicKey] = $val;
        $this->id = $val;
        $this->loadValuesFromClass();
    }

    /**
     * @return array|int|string
     */
    function remove() {
        $query = "DELETE FROM ".static::$tbl_name." where `".self::getUnicKey()."`=".$this->getId();
        $result = db::instance()->query($query);
        return $result;
    }

    /**
     * 
     * @throws query_exception
     */
    function save() {

        $this->beforeSave();

        try {

            $this->getId();
            $this->update();

        } catch (record_exception $e) {

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
        $query .= " WHERE ".$idKey."=".$this->getId();
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
        $vals = $this->origin_vals;

        try {
            $this->getId();
        } catch (record_exception $e) {
            $vals = $this->vals;
        }

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