<?php
namespace core;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of include_view
 *
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class include_view {
    //put your code here
    
    protected $name;
    protected $data;
    
    public function __construct($name, $data) {
        $this->name = $name;
        $this->data = $data;
    }
    
    protected $asset;
    
    function add_asset(asset $asset) {
        $this->asset = $asset;
    }
    
    function get_asset() {
        return $this->asset;
    }
    
    function get_name() { 
        return $this->name; 
    }
    
    function get_data() { 
        return $this->data;    
    }
    
    protected $include_views = array();
    
    function include_view($var_name, include_view $view) {
        
        if (array_key_exists($var_name, $this->include_views) !== false)
        {
            $this->include_views[$var_name][] = $view;
        } 
        else
        {
            $this->include_views[$var_name] = $view;
        }
    }
    
    function get_includes() {
        return $this->include_views;
    }
}

?>