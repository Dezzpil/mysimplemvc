<?php
/**
 * Created by PhpStorm.
 * User: dezzpil
 * Date: 4/19/14
 * Time: 2:29 AM
 */

namespace msmvc\modules;

class menu_item {

    /**
     * @return menu_item
     */
    static function forge() {
        return new self;
    }

    protected $name, $uri, $active;

    function set_uri($uri) {
        $this->uri = $uri;
        return $this;
    }

    function set_name($name) {
        $this->name = $name;
        return $this;
    }

    function set_active($active) {
        $this->active = $active;
        return $this;
    }

    /**
     * Получить uri
     * @return string
     */
    function get_uri() {
        return $this->uri;
    }

    /**
     * Получить описание ссылки
     * @return string
     */
    function get_name() {
        return $this->name;
    }

    /**
     * Получить состояние активности
     * @return bool
     */
    function get_active() {
        return $this->active;
    }
}