<?php

namespace sql;

/**
 * Model 
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class query_where
{
    static function create($condition = null) {
        return new self($condition);
    }
    
    public function __construct($condition) {
        if ($condition !== null) {
            $this->where = $condition;
        }
    }
    
    protected $where = null;
    protected $and_list = array();
    protected $or_list = array();
    
    function add_and($condition) {
        
        if (is_string($condition)) 
        {
            $this->and_list[] = $condition;
        } 
        else if ($condition instanceof query_where) 
        {
            $this->and_list[] = '(' . $condition->get() . ')';
        }
        return $this;
    }
    
    function add_or($condition) {
        if (is_string($condition)) 
        {
            $this->or_list[] = $condition;
        } 
        else if ($condition instanceof query_where) 
        {
            $this->or_list[] = '(' . $condition->get() . ')';
        }
        return $this;
    }
    
    function get() {
        $and_condition_str = '';
        $or_condition_str = '';
        $where = $this->where;
        
        if ( ! empty($this->and_list)) {
            $and_condition_str = join('AND ', $this->and_list);
            $where .= ' AND '.$and_condition_str;
        }
        
        if ( ! empty($this->or_list)) {
            $or_condition_str = join('OR ', $this->or_list);
            $where .= ' OR '.$or_condition_str;
        }
        
        return $where;
    }
    
    public function get_prepared() {
        return " WHERE ".$this->get();
    }
}

?>
