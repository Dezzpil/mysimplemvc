<?php

namespace msmvc\core;

use msmvc\sql;
use msmvc\help;

/**
 * Common MYSQL base connector
 * @author Nikita Dezzpil Orlov
 * @todo find useful and popular ORM lib and use it as dependency
 */
class db {

    static protected $instance;
    
    static function instance() {
        if (! isset(self::$instance)) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }
    
    public $handle = null;

    function __construct() {
        //$this->handle = mysql_connect(DB_HOST, DB_USER, DB_PASS);
        $this->handle = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
        
        if ($this->handle === FALSE) {
            throw new db_exception('Can\'t connect to MySQL');
        }
        
        mysqli_select_db($this->handle, DB_NAME);

        //mysqli_query($this->handle, 'SET NAMES UTF-8');
        mysqli_set_charset($this->handle, 'utf8');
    }
    
    static public $last_query = null;
    static public $last_error = null;
    function query($query) {
        self::$last_query = $query;
        
        try {
            $res = mysqli_query($this->handle, $query);

            // операции DML
            if (is_bool($res)) {
                return mysqli_insert_id($this->handle);
            }

            // опреации DDL
            $result = array();
            while ($r = mysqli_fetch_assoc($res)) {
                $result[] = $r;
            }
            
            return $result;
        } 
        catch (Exception $e) {
            self::$last_error = new query_exception(
                mysqli_error($this->handle),
                mysqli_errno($this->handle)
            );
            throw self::$last_error;
        }
    }

    function _prepare_string($string) {
        return mysqli_real_escape_string($this->handle, $string);
    }

    static function prepare_string($string) {
        return self::instance()->_prepare_string($string);
    }
    
    function get_fields($tbl_name, $active_record = FALSE) {
        try  {
            $tbl_name = $this->_prepare_string($tbl_name);
            $query = "SHOW COLUMNS FROM $tbl_name";

            $result = $this->query($query);

            if ($active_record) {
                foreach ($result as & $field) {
                    if ($field['Key'] == 'PRI' && $field['Extra'] == 'auto_increment') {
                        unset($field);
                    }
                }
            }
        } catch (Exception $e) {
            self::$last_error = new query_exception(
                mysqli_error($this->handle),
                mysqli_errno($this->handle)
            );
            throw self::$last_error;
        }
        
        return $result;
    }
}
?>