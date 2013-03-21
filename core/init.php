<?php
/**
 * init.php
 * uses in .htaccess for init MVC
 * 
 * @see .htaccess in root
 */
include("../config/config.php");

include_once(ABS_CORE_PATH.'mvc.php');
include_once(ABS_CORE_PATH.'model/ajax.php');
include_once(ABS_CORE_PATH.'model/array.php');
include_once(ABS_CORE_PATH.'model/num.php');
include_once(ABS_CORE_PATH.'model/session.php');
include_once(ABS_CORE_PATH.'model/validator.php');
include_once(ABS_CORE_PATH.'model/xhelp.php');

mvc::set_default('controller_prefix', 'controller_');
mvc::set_default('model_prefix', 'model_');

global $MVC;
$MVC = mvc::instance();

$MVC->request($_SERVER['REQUEST_URI']);
?>