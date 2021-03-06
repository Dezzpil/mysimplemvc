<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 1/14/14
 * Time: 12:52 AM
 * To change this template use File | Settings | File Templates.
 */

namespace msmvc;

/**
 * Class request
 * @package msmvc\core
 */
class request {

    protected static $instance = null;

    /**
     * @return request
     * @throws exception_request
     */
    static function instance() {

        if (self::$instance === null) {

            /**
             * if we use phpunit, for example,
             * we don't need to start request handling
             */
            if (
                array_key_exists('REQUEST_URI', $_SERVER) !== false
                && ! empty($_SERVER['REQUEST_URI'])
            ) {
                // only if there was any request
                self::$instance = new self($_SERVER['REQUEST_URI']);

            } else {

                throw new exception_request('no request uri');

            }

        }

        return self::$instance;

    }

    protected function __clone() {}
    protected function __wakeUp() {}

    protected  function __construct($request) {

        $this->request_uri = $request;
        $mvc_request = $this->parse_request_uri($request);

        $names = $this->collect_names($mvc_request);

        $this->action_name = $names['action'];
        $this->controller_name = $names['controller'];
    }

    protected function collect_names($parts) {
        $action_name = array_pop($parts);
        $controller_name = join('/', $parts);

        return array(
            'controller' => $controller_name,
            'action' => $action_name
        );
    }

    /**
     * Получить массив, где
     * 0 - имя контроллера (может быть вложенным именем, типа api/sign),
     * 1 - имя действия контроллера
     * @param string $request
     * @return array
     */
    protected function parse_request_uri($request) {
        $request = parse_url($request, PHP_URL_PATH);
        $tmp_ar_request = explode('/', $request);

        // отсеим пустые значения после explode
        // и тем самым соберем финальный массив обращения
        // где 0 - имя контроллера, 1 - действия
        $ar_request = array();
        foreach($tmp_ar_request as $part) {
            $part = trim($part);
            if (strlen($part) > 0) $ar_request[] = $part;
        }


        if (count($ar_request) == 0) {
            // пустой REQUEST_URI
            return array(
                mvc::instance()->getDefault(mvc::KEY_CONTROLLER_NAME),
                mvc::instance()->getDefault(mvc::KEY_ACTION_NAME)
            );
        }

        if (count($ar_request) == 1) {
            // REQUEST_URI указывает только на контроллер
            return array(
                $ar_request[0],
                mvc::instance()->getDefault(mvc::KEY_ACTION_NAME)
            );
        }

        // действие может содержать параметры запроса
        // тогде вынесем их в param1, param2

        return $ar_request;
    }

    /**
     * @return string
     */
    function get_request_uri() {
        return $this->request_uri;
    }

    /**
     * @return string
     */
    function get_controller_name() {
        return $this->controller_name;
    }

    /**
     * @return string
     */
    function get_action_name() {
        return $this->action_name;
    }

    /**
     * Проверить соответсвие указанного uri текущему
     * в терминах контроллера и действия
     * @param string $uri
     * @return bool
     */
    function is_equal($uri) {
        $result = $this->parse_request_uri($uri);

        $names = $this->collect_names($result);
        $controller_name = $names['controller'];
        $action_name = $names['action'];

        if (
            $controller_name == $this->get_controller_name() &&
            $action_name == $this->get_action_name()
        ) {
            return true;
        }

        return false;
    }
}