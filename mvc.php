<?php
use \core;

/**
 * Description of mvc
 *
 * @author Dezzpil
 */
class mvc 
{
    static private $_default = array(
        'template'          => 'base',
        'controller'        => 'index',
        'action'            => 'index',
        'controller_prefix' => 'controller_',
        'model_prefix'      => 'model_'
    );
    
    static private $_instance;
 
    static public function instance()
    {
        if ( ! isset(self::$_instance))
        {
            self::$_instance = new self;
        }
        return self::$_instance;
    }    

    static function set_default($key, $value)
    {
        if (array_key_exists($key, self::$_default))
        {
            self::$_default[$key] = $value;
            return TRUE;
        }
        return FALSE;
    }
    
    static function load_models($class_name)
    {
        $name = explode('_', $class_name);
        if ($name[0] != 'model')
        {
            // модель должна быть с префиксом model
            return FALSE;
        }
        
        $name_part_count = count($name);
        if ($name_part_count > 2)
        {
            // частей в названии модели более 2, значит
            // файл модели находится в подкаталогах
            $path = ABS_MODEL_PATH;
            array_shift($name);
            $name_part_count--;
            while ($name_part_count > 1)
            {
                $path .= array_shift($name).'/';
                $name_part_count--;
            }
            $path .= array_pop($name).EXT;
        }
        else
        {
            // модель находится в папке models
            $path = ABS_MODEL_PATH.$name[1].EXT;
        }

        include_once $path;
    }
    
    static function load_controllers($class_name)
    {
        $name = explode('_', $class_name);
        if ($name[0] != 'controller') return FALSE;
        
        $path = ABS_CONTROLLER_PATH.$name[1].EXT;
        include_once $path;
    }
    
    static function redirect($request)
    {
        header("Location: ".ROOT_PATH.$request);
        die('redirect');
    }
    
    private $controller;
    private $view;
    
    function __construct()
    {
        // классы для работы с представлением
        include_once ABS_CORE_PATH.'view'.EXT;
        include_once ABS_CORE_PATH.'include_view'.EXT;
        include_once ABS_CORE_PATH.'asset'.EXT;
        
        // подключим свой класс для обработки ошибок, чтобы
        // ничего не светить
        include_once ABS_CORE_PATH.'mvc_exception'.EXT;
        
		if ( ! NO_DB_USING)
		{
			// класс соединения с бд
			include_once ABS_CORE_PATH.'db'.EXT;
			
			// попробуем подключиться
			$this->DB = db::instance();
		}
        
        spl_autoload_register(array('mvc', 'load_models'));
    }
    
    function request($request)
    {
        $mvc_request = $this->parse_request_uri($request);
        
        $file_name = $mvc_request[0];
        $action_name = $mvc_request[1];
        $params = $mvc_request[2];
        
        if (count($mvc_request) == 3)
        {
            //$params = $this->parse_get_params($mvc_request[2]);
        }
        
        try 
        {
            
            
            $this->exec_controller($file_name, $action_name, $params);
            $this->render();
        }
        catch (mvc_exception $e)
        {
            throw new mvc_exception($e->message);
        }
        
    }
    
    /**
     * Получить массив, где 0 - имя контроллера, 1 - имя действия контроллера, 2 - параметры (если есть)
     * @param string $request
     * @return array 
     */
    function parse_request_uri($request)
    {
        $tmp_ar_request = explode('/', $request);
        
        // отсеим пустые значения после explode
        // и тем самым соберем финальный массив обращения
        // где 0 - имя контроллера, 1 - действия
        $ar_request = array();
        foreach($tmp_ar_request as $part)
        {
            $part = trim($part);
            if (strlen($part) > 0) $ar_request[] = $part;
        }

        
        if (count($ar_request) == 0)
        {
            // пустой REQUEST_URI
            return array(self::$_default['controller'], self::$_default['action']);
        }
        
        if (count($ar_request) == 1)
        {
            // REQUEST_URI указывает только на контроллер
            return array($ar_request[0], self::$_default['action']);
        }

        // действие может содержать параметры запроса
        // тогде вынесем их в ключ == 2
        if (stripos($ar_request[1], '?') !== FALSE)
        {
            $tmp_ar_request = explode('?', $ar_request[1]);
            $ar_request[1] = $tmp_ar_request[0];
            $ar_request[2] = $tmp_ar_request[1];
        }
        
        return $ar_request;
    }
    
    function render()
    {
        echo view::render();
        die;
    }
    
    function exec_controller($file_name, $action_name, $params)
    {
        include_once ABS_CORE_PATH.'controller'.EXT;
        
        // подключаем файл с искомым контроллером
        spl_autoload_register(array('mvc', 'load_controllers'));
        $class_name = self::$_default['controller_prefix'].$file_name;
        
        // Выполнение
        $this->controller = new $class_name;
        $this->controller->before();
        $this->controller->$action_name($params);
        $this->controller->after();
    }
}
?>