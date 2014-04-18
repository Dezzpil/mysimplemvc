<?php

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
 * we may choose view class for msmvc
 * $view = new core\view_cli();
 */

$view = new core\view_html();
$msmvc->setViewType($view);

/**
 * start session
 */

\msmvc\help\session::instance();

try {

    $request = core\request::instance();
    $msmvc->run($request);

} catch (core\request_exception $e) {

    /**
     * we may add some logger here
     */
    var_dump($e);
}

unset($msmvc);
?>