<?php
namespace msmvc;

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
     * @throws exception_mvc
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

    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {
		if ( ! NO_DB_USING) {
			$this->DB = db::instance();
		}
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
     * @throws exception_mvc
     */
    function run(request $request, $params = array()) {
        try {
            $this->exec_controller($request->get_controller_name(), $request->get_action_name(), $params);
            $this->render();
        } catch (exception_mvc $e) {
            throw new exception_mvc($e->message);
        }
    }

    function render() {
        die($this->view->complete());
    }
    
    function exec_controller($file_name, $action_name, $params = array()) {

        // подключаем файл с искомым контроллером
        $file_name = str_replace('/', '\\', $file_name);
        $class_name = APP_NAMESPACE.'\\'.$this->defaultOpts[self::KEY_NAMESPACE_CONTROLLERS].'\\'.$file_name;

        $path = self::convertToPath($class_name);

        if (is_readable($path)) {

            $obj = new $class_name($this->view);

            if ($obj instanceof controller) {

                $this->controller = new $class_name($this->view);

                $this->controller->before();

                if ( ! method_exists($this->controller, $action_name)) {
                    new exception_mvc(exception_mvc::ERROR_404);
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
                new exception_mvc(exception_mvc::ERROR_403);

            }
        } else {
            //new mvc_exception(mvc_exception::ERROR_404);
        }
    }
}
?>