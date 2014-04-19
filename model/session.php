<?php
namespace msmvc\model;

/**
 * Simple for file session
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 * @todo rewrite with driver support
 */
class session
{
    static protected $instance = null;
    
    static function instance() {
        if (self::$instance === null) {
            self::start();
            self::$instance = new self;
        }
        
        return self::$instance;
    }

    static function isStarted() {
        return session_id();
    }
    
        
    static function start() {
        session_start();
    }
    
    static function close() {
        session_write_close();
        self::$instance = null;
    }
    
    private function __construct() {
        // инстанцируется только через Синглтон 
    }
    
    function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    function get($key = '') {
        if (empty($key)) return $_SESSION;
        return arr::get($key, $_SESSION);
    }
    
    function remove($key) {
        if (array_key_exists($key, $_SESSION) !== false) {
            $_SESSION[$key] = null;
            unset($_SESSION[$key]);
        }
    }
}
?>