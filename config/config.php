<?php

/**
 * config.php 
 * используется для настройки путей на разных серверах
 * и указание фундаментальных констант, используемых во всей
 * системе в целом
 */

// Constants
define('HOST', 'WRITE YOUR HOST NAME HERE!');

// relation pathes
define('ROOT_PATH', '/');
define('CORE_PATH', ROOT_PATH.'core/');
define('IMAGES_PATH', ROOT_PATH.'images/');
define('JS_PATH', ROOT_PATH.'js/');
define('CSS_PATH', ROOT_PATH.'css/');

define('TEMPLATE_PATH', ROOT_PATH.'views/template/');
define('ZONE_PATH', ROOT_PATH.'views/zones/');
define('MODEL_PATH', ROOT_PATH.'models/');
define('CONTROLLER_PATH', ROOT_PATH.'controllers/');

// absolute pathes
define('ABS_ROOT_PATH', 'WRITE YOUR FULL PATH HERE!');
define('ABS_CORE_PATH', ABS_ROOT_PATH.'core/');
define('ABS_IMAGES_PATH', ABS_ROOT_PATH.'images/');
define('ABS_MODEL_PATH', ABS_ROOT_PATH.'models/');
define('ABS_MODUL_PATH', ABS_MODEL_PATH.'modules/');

define('ABS_TEMPLATE_PATH', ABS_ROOT_PATH.'views/template/');
define('ABS_ZONE_PATH', ABS_ROOT_PATH.'views/zones/');
define('ABS_MODEL_PATH', ABS_ROOT_PATH.'models/');
define('ABS_CONTROLLER_PATH', ABS_ROOT_PATH.'controllers/');

define('EXT', '.php');

// Database
define('NO_DB_USING', false);
define("DB_HOST", "localhost");
define("DB_NAME", "mymoney");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_SALT", "SVEN123");

// Mail settings
define('SMTP_SERVER', '');
define('SMTP_LOGIN', '');
define('SMTP_PASSWORD', '');
define('SMTP_PORT', '');
define('SENDMAIL', '');

// Session
ini_set('session.use_cookies', 1);
ini_set('session.use_trans_sid', 1);
session_start();

?>