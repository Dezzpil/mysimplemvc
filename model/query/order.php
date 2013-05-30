<?php
namespace sql;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of order
 *
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class query_order {
    //put your code here
    
    const ASC = 'ASC';
    const DESC = 'DESC';
    
    protected $orders = array();

    static function forge($key, $order) {
        return new self($key, $order);
    }
    
    public function __construct($key, $order) {
        $this->orders[] = array($key, $order);
    }
    
    function set($key, $order) {
        $this->orders = array();
        $this->orders[] = array($key, $order);
    }
    
    function add($key, $order) {
        $this->orders[] = array($key, $order);
    }
    
    function get_prepared() {
        $order_str = ' ORDER BY ';
        foreach ($this->orders as $order) {
            $order_str .= $order[0].' '.$order[1].', ';
        }
        
        $order_str = substr($order_str, 0, strlen($order_str) - 2);
        return $order_str;
    }
}

?>
