<?php

/**
 * Simple for file session
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class session
{
    static protected $instance = null;
    
    static function instance()
    {
        if (self::$instance === null)
        {
            self::$instance = new self;
        }
        
        return self::$instance;
    }
    
    private function __construct()
    {
        // инстанцируется только через Синглтон 
    }
    
    function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    function get($key = '')
    {
        if (empty($key)) return $_SESSION;
        
        return @$_SESSION[$key];
    }
    
    function remove($key)
    {
        if (array_key_exists($key, $_SESSION) !== false)
        {
            unset($_SESSION[$key]);
        }
    }
}
?>