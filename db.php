<?php

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
            throw new mvc_exception('Невозможно подключиться к базе. Забей :)');
        }
        
        mysql_select_db(DB_NAME, $this->handle);
    }
    
    function query($query)
    {
        $res = mysql_query($query);
        
        // операции DML
        if (is_bool($res)) 
        {   
            $id = mysql_insert_id();
            return $id;
        }

        // опреации DDL
        $result = array();
        while ($r = mysql_fetch_assoc($res))
        {
            $result[] = $r;
        }

        return $result;
    }
    
    function get_fields($tbl_name, $active_record = FALSE)
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
        
        return $result;
    }
}
?>