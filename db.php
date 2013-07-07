<?php

use \sql;
use \help;

/**
 * Common MYSQL base connector
 * @author Nikita Dezzpil Orlov
 */
class db
{
    static protected $instance;
    
    static function instance($param = FALSE)
    {
        if (! isset(self::$instance))
        {
            self::$instance = new self($param);
        }
        
        return self::$instance;
    }
    
    public $handle = FALSE;
    function __construct()
    {
        $this->handle = mysql_connect(DB_HOST, DB_USER, DB_PASS);
        
        if ($this->handle === FALSE)
        {
            throw new db_exception('Невозможно подключиться к базе');
        }
        
        mysql_select_db(DB_NAME, $this->handle);
        mysql_query('SET NAMES UTF-8');

    }
    
    static public $last_query = null;
    static public $last_error = null;
    function query($query)
    {
        self::$last_query = $query;
        
        try 
        {
            $res = mysql_query($query);

            // операции DML
            if (is_bool($res)) 
            {   
                return mysql_insert_id();
            }

            // опреации DDL
            $result = array();
            while ($r = mysql_fetch_assoc($res)) 
            {
                $result[] = $r;
            }
            
            return $result;
        } 
        catch (Exception $e) 
        {
            self::$last_error = new query_exception(mysql_error(), mysql_errno());
            throw self::$last_error;
        }
    }
    
    static function prepare_string($string) {
        return mysql_real_escape_string($string);
    }
    
    function get_fields($tbl_name, $active_record = FALSE)
    {
        try 
        {
            $tbl_name = mysql_real_escape_string($tbl_name);
            $query = "SHOW COLUMNS FROM $tbl_name";

            $result = $this->query($query);

            if ($active_record)
            {
                foreach ($result as & $field)
                {
                    if ($field['Key'] == 'PRI' && $field['Extra'] == 'auto_increment')
                    {
                        unset($field);
                    }
                }
            }
        } catch (Exception $e) {
            self::$last_error = new query_exception(mysql_error(), mysql_errno());
            throw self::$last_error;
        }
        
        return $result;
    }
}
?>