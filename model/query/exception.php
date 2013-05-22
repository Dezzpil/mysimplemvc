<?php
namespace sql;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of queryException
 *
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class query_exception extends \Exception {
    
    public function __construct($message, $code, $previous) {
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString() {
        echo "<pre>"; parent::__toString(); echo "</pre>";
    }
}

?>
