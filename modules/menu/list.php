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

    protected static $instances = array();

    /**
     * @return menu_list
     */
    static function instance() {

        $key = get_called_class();

        if (
            array_key_exists($key, self::$instances) === false ||
            self::$instances[$key] === null
        ) {

            $menu = new static;

            $request = request::instance();
            foreach ($menu->items as $uri => $item) {
                if ($request->is_equal($uri)) {
                    $menu->items[$uri]['active'] = true;
                } else {
                    $menu->items[$uri]['active'] = false;
                }
            }

            self::$instances[$key] = $menu;
        }

        return self::$instances[$key];

    }

    protected $items = array();

    protected function __construct() {}
    protected function __clone() {}
    protected function __wakeUp() {}

    /**
     * Получить список состоящий из элементов menu_item
     * @return array
     */
    function get_list() {
        $list = array();
        foreach ($this->items as $uri => $item) {
            $menu_item = menu_item::forge()
                ->set_uri($uri)
                ->set_name($item['name'])
                ->set_active($item['active']);

            array_push($list, $menu_item);
        }

        return $list;
    }

}