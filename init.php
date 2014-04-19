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


$msmvc = \msmvc\mvc::instance();

/**
 * we may choose view class for msmvc
 * $view = new core\view_cli();
 */

$view = new \msmvc\view_html();
$msmvc->setViewType($view);

/**
 * start session
 */

\msmvc\model\session::instance();

try {

    $request = \msmvc\request::instance();
    $msmvc->run($request);

} catch (\msmvc\exception_request $e) {

    /**
     * we may add some logger here
     */
    var_dump($e);
}

unset($msmvc);
?>