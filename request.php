<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 1/14/14
 * Time: 12:52 AM
 * To change this template use File | Settings | File Templates.
 */

namespace msmvc\core;

/**
 * Class request
 * @package msmvc\core
 * @todo make
 */
class request {

    static function forge() {
        return new self($_REQUEST);
    }

    private function __construct($request) {
        var_dump($request);
    }

}