<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dezzpil
 * Date: 1/14/14
 * Time: 11:15 AM
 * To change this template use File | Settings | File Templates.
 */

namespace msmvc\core;
use msmvc\help\arr;

/**
 * Class response
 * @package msmvc\core
 */
class response {


    protected $error = null;

    /**
     * @return bool
     */
    function withErrors() {
        return !!$this->error;
    }

    /**
     * @param \Exception $e
     */
    function setError(\Exception $e) {
        $this->error = $e;
    }

    /**
     * @return null
     */
    function getError() {
        return $this->error;
    }
    

    protected $data = array();

    /**
     * @return bool
     */
    function withData() {
        return !empty($this->data);
    }

    /**
     * @param $key
     * @param string $def
     * @return mixed
     */
    function getData($key, $def = '') {
        return arr::get($key, $this->data, $def);
    }

    /**
     * @param $key
     * @param $val
     */
    function setData($key, $val) {
        $this->data[$key] = $val;
    }
}