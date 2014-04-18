<?php
namespace msmvc\core;

/**
 * Description of mvc
 *
 * @author Dezzpil
 */
class mvc {

    static protected $_instance = null;
    static protected $namespace = 'msmvc';

    /**
     * @return mvc
     */
    static public function instance() {
        if ( ! isset(self::$_instance)) {
            /**
             * PSR-0
             * @link https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
             */
            spl_autoload_register(function($className) {

                $path = self::convertToPath($className);
                if (is_readable($path))
                    include_once($path);
                else
                    return false;
            });

            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * @param $className
     * @return string
     */
    static function convertToPath($className) {

        $path = '';
        $namespaces = explode('\\', $className);
        $folders = explode('_', array_pop($namespaces));

        if ( ! empty($namespaces)) {
            if ($namespaces[0] == self::$namespace) { // выбираем файлы движка, включая модули
                $path = ABS_CORE_PATH;
                array_shift($namespaces);
            } else if ($namespaces[0] == APP_NAMESPACE) { // выбираем файлы приложения
                $path = ABS_ROOT_PATH;
                array_shift($namespaces);
            } else {                                 // выбираем файлы вендоров
                $path = ABS_VENDOR_PATH;
            }

            while (count($namespaces) > 0) {
                $path .= array_shift($namespaces).DIRECTORY_SEPARATOR;
            }
        }

        while (count($folders) > 0) {
            $path .= array_shift($folders).DIRECTORY_SEPARATOR;
        }

        return substr($path, 0, -1).'.php';
    }

    /**
     * @param $request
     * @deprecated use mvc::redirectHeader($uri);
     */
    static function redirect($request) {
        self::instance()->request($request);
    }

    /**
     * @deprecated use mvc::instance()->run();
     * @param string $controllerFile (without prefix)
     * @param string $actionName
     * @param array $params
     * @throws mvc_exception
     */
    static function redirectController($controllerFile, $actionName, $params = array()) {
        self::instance()->run($controllerFile, $actionName, $params);
    }

    /**
     * @param string $request
     */
    static function redirectHeader($request){
        header($request);
        die;
    }
    
    private $controller;
    private $view;
    
    private function __construct() {

        // классы для работы с представлением
        include_once 'view/interface.php';
        include_once 'view/exceptions.php';
        include_once 'view/prefab.php';
        include_once 'view/asset.php';
        include_once 'view/html.php';
        include_once 'view/cli.php';

        include_once 'request_exception.php';
        include_once 'request.php';

        // include_once 'response.php';

        include_once 'mvc_exception.php';

        include_once 'controller.php'; // root controller

		if ( ! NO_DB_USING) {
			// класс соединения с бд
			include_once 'db.php';
            include_once 'db_exception.php';
			
			// попробуем подключиться
			$this->DB = db::instance();
		}

        include_once('model/ajax.php');
        include_once('model/arr.php');
        include_once('model/num.php');
        include_once('model/session.php');
        include_once('model/validator.php');
        include_once('model/xhelp.php');
        include_once('model/record.php');
        include_once('model/charset.php');
        include_once('model/str.php');
        include_once('model/exception/record.php');
        include_once('model/exception/norecord.php');
        include_once('model/query/exception.php');
        include_once('model/query/where.php');
        include_once('model/query/order.php');
    }

    const KEY_CONTROLLER_NAME = 'controller';
    const KEY_ACTION_NAME = 'action';
    const KEY_NAMESPACE = 'namespace';
    const KEY_NAMESPACE_CONTROLLERS = 'namespace_controllers';
    const KEY_NAMESPACE_MODELS = 'namespace_models';
    const KEY_NAMESPACE_MODULES = 'namespace_modules';

    private $defaultOpts = array(
        'controller'            => 'index',
        'action'                => 'index',
        'namespace'             => 'newApp',
        'namespace_models'      => 'models',
        'namespace_controllers' => 'controllers',
        'namespace_modules'     => 'modules'
    );

    /**
     * Переопределить значение по умолчанию:
     *   'controller'            => 'index',
     *   'action'                => 'index',
     *   'namespace'             => 'newApp',
     *   'namespace_models'      => 'models',
     *   'namespace_controllers' => 'controllers',
     *   'namespace_modules'     => 'modules'
     *
     * @param string $key смотри константы mvc!
     * @param $value
     * @return $this
     */
    function setDefault($key, $value) {
        if (array_key_exists($key, $this->defaultOpts) !== false) {
            $this->defaultOpts[$key] = $value;
        }
        return $this;
    }

    /**
     * Получить значение по умолчанию
     * @param $key
     * @return mixed
     */
    function getDefault($key) {
        return @$this->defaultOpts[$key];
    }

    /**
     * Установить тип представления (html,cli,...)
     * @param view_interface $view
     */
    function setViewType(view_interface $view) {
        $this->view = $view;
    }

    /**
     * Выполнить запрос
     * @param request $request
     * @param array $params
     * @throws mvc_exception
     */
    function run(request $request, $params = array()) {
        try {
            $this->exec_controller($request->get_controller_name(), $request->get_action_name(), $params);
            $this->render();
        } catch (mvc_exception $e) {
            throw new mvc_exception($e->message);
        }
    }

    function render() {
        die($this->view->complete());
    }
    
    function exec_controller($file_name, $action_name, $params = array()) {

        // подключаем файл с искомым контроллером
        $class_name = APP_NAMESPACE.'\\'.$this->defaultOpts[self::KEY_NAMESPACE_CONTROLLERS].'\\'.$file_name;
        $path = self::convertToPath($class_name);

        if (is_readable($path)) {

            $obj = new $class_name($this->view);

            if ($obj instanceof controller) {

                $this->controller = new $class_name($this->view);

                $this->controller->before();

                if ( ! method_exists($this->controller, $action_name)) {
                    new mvc_exception(mvc_exception::ERROR_404);
                }

                if (! empty($params)) {
                    call_user_func_array(array($this->controller, $action_name), $params);
                } else {
                    $this->controller->$action_name();
                }

//                $response = $this->controller->$action_name();
//                if ( ! $response instanceof response) {
//                    throw new mvc_exception('Action of controller must return response instance');
//                }

                $this->controller->after();
                //return $response;

            } else {

                // not a controller
                new mvc_exception(mvc_exception::ERROR_403);

            }
        } else {
            new mvc_exception(mvc_exception::ERROR_404);
        }
    }
}
?>