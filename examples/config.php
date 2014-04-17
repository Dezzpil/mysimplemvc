<?php

setlocale(LC_ALL, 'ru_RU.UTF8', 'rus_RUS.UTF8', 'Russia');
date_default_timezone_set('Europe/Moscow');

define('APP_NAMESPACE', 'myapp');

// Constants
if (array_key_exists('HTTP_HOST', $_SERVER) !== false) {
    define('HOST', $_SERVER['HTTP_HOST']);
}

define('BASE_CHARSET', 'utf-8');
mb_internal_encoding(BASE_CHARSET);


if (
    array_key_exists('DOCUMENT_ROOT', $_SERVER) !== false
    && ! empty($_SERVER['DOCUMENT_ROOT'])
) {
    // basically
    define('ABS_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/');

} else if (array_key_exists('PWD', $_SERVER) !== false) {

    // phpunit, from cli
    define('ABS_ROOT_PATH', $_SERVER['PWD'].'/');

}
define('ABS_CORE_PATH', ABS_ROOT_PATH.'core/');
define('ABS_VENDOR_PATH', ABS_ROOT_PATH.'vendor/');
define('ABS_ERROR_PATH', ABS_ROOT_PATH.'errors/');

define('ROOT_PATH', '/');

// For MySQL database
define('NO_DB_USING', false);
define("DB_HOST", "localhost");
define("DB_NAME", "myapp");
define("DB_USER", "root");
define("DB_PASS", "toor");
define("DB_SALT", "");

// For Composer
require ABS_VENDOR_PATH.'autoload.php';