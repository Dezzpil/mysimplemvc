<?php
/**
 * init.php
 * uses in .htaccess for init MVC
 * 
 * @author Nick Dezzpil Orlov <n.dezz.orlov@gmail.com> 
 * @see .htaccess in root
 */

/**
 * you may create your own config
 */

$configPath = __DIR__.'/../configs/config.php';
if (is_readable($configPath)) {
    require_once($configPath);
}

/**
 * transform error to Exceptions
 */

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler", E_ALL | E_ERROR | E_STRICT);

/**
 * include and start msmvc
 */

include_once(ABS_CORE_PATH.'mvc.php');
use \msmvc\core;
$msmvc = core\mvc::instance();

/**
 * set default values
 */

$msmvc->setDefault(core\mvc::KEY_CONTROLLER_NAME, 'index');
$msmvc->setDefault(core\mvc::KEY_ACTION_NAME, 'index');
$msmvc->setDefault(core\mvc::KEY_NAMESPACE, APP_NAMESPACE);
$msmvc->setDefault(core\mvc::KEY_NAMESPACE_MODELS, 'models');
$msmvc->setDefault(core\mvc::KEY_NAMESPACE_CONTROLLERS, 'controllers');

/**
 * we may choose view class for msmvc
 * $view = new core\view_cli();
 */

$view = new core\view_html();
$msmvc->setView($view);

/**
 * start session
 */

\msmvc\help\session::instance();

try {

    /**
     * if we use phpunit, for example,
     * we don't need to start request handling
     */
    if (
        array_key_exists('REQUEST_URI', $_SERVER) !== false
        && ! empty($_SERVER['REQUEST_URI'])
    ) {
        // only if there was any request
        $msmvc->request($_SERVER['REQUEST_URI']);
    }

} catch (ErrorException $e) {

    /**
     * we may add some logger here
     */
    var_dump($e);
}

unset($msmvc);
?>