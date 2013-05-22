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
@include("../config/config.php");

// Constants
defined('HOST') || define('HOST', $_SERVER['HTTP_HOST']);
defined('ABS_ROOT_PATH') || define('ABS_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/');

// Database
defined('NO_DB_USING') || define('NO_DB_USING', false);
defined('DB_HOST') || define("DB_HOST", "localhost");
defined('DB_NAME') || define("DB_NAME", "");
defined('DB_USER') || define("DB_USER", "");
defined('DB_PASS') || define("DB_PASS", "");
defined('DB_SALT') || define("DB_SALT", "");

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
define('ABS_CORE_PATH', ABS_ROOT_PATH.'core/');
define('ABS_IMAGES_PATH', ABS_ROOT_PATH.'images/');
define('ABS_MODEL_PATH', ABS_ROOT_PATH.'models/');
define('ABS_MODUL_PATH', ABS_MODEL_PATH.'modules/');

define('ABS_TEMPLATE_PATH', ABS_ROOT_PATH.'views/template/');
define('ABS_ZONE_PATH', ABS_ROOT_PATH.'views/zones/');
define('ABS_MODEL_PATH', ABS_ROOT_PATH.'models/');
define('ABS_CONTROLLER_PATH', ABS_ROOT_PATH.'controllers/');

define('EXT', '.php');

/**
 * include core models
 * its all in help\ namespace
 * @todo подключать модульно!
 */
include_once(ABS_CORE_PATH.'mvc.php');
include_once(ABS_CORE_PATH.'model/ajax.php');
include_once(ABS_CORE_PATH.'model/array.php');
include_once(ABS_CORE_PATH.'model/num.php');
include_once(ABS_CORE_PATH.'model/session.php');
include_once(ABS_CORE_PATH.'model/validator.php');
include_once(ABS_CORE_PATH.'model/xhelp.php');
include_once(ABS_CORE_PATH.'model/query/exception.php');
include_once(ABS_CORE_PATH.'model/query/where.php');
include_once(ABS_CORE_PATH.'model/query/order.php');

mvc::set_default('controller_prefix', 'controller_');
mvc::set_default('model_prefix', 'model_');

global $MVC;
$MVC = mvc::instance();

$MVC->request($_SERVER['REQUEST_URI']);
?>