<?php

namespace msmvc\modules;

use msmvc\request;

/**
 * Class menu
 * Наследуемся и определяем собственный список элементов
 * меню - в $items, следующего формата
 * 'uri' => array( 'name' => %name% )
 *
 * @package msmvc\modules
 */
class menu_list {

    protected static $items = array();
    protected static $instance = null;

    /**
     * @return menu_list
     */
    static function instance() {

        if (static::$instance === null) {

            $request = request::instance();
            foreach (static::$items as $uri => $item) {
                if ($request->is_equal($uri)) {
                    static::$items[$uri]['active'] = true;
                } else {
                    static::$items[$uri]['active'] = false;
                }
            }

            static::$instance = new static;
        }

        return static::$instance;

    }

    protected function __construct() {}
    protected function __clone() {}
    protected function __wakeUp() {}

    /**
     * Получить список состоящий из элементов menu_item
     * @return array
     */
    function get_list() {
        $list = array();
        foreach (static::$items as $uri => $item) {
            $menu_item = menu_item::forge()
                ->set_uri($uri)
                ->set_name($item['name'])
                ->set_active($item['active']);

            array_push($list, $menu_item);
        }

        return $list;
    }

}