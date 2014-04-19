<?php

namespace msmvc\model;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of queryException
 *
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class exception_query extends \Exception {
    
    public function __construct($message, $code, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString() {
        return "<pre>".parent::__toString()."</pre>";
    }
}

?>