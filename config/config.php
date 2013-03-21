<?php

/**
 * config.php 
 * используется для настройки путей на разных серверах
 * и указание фундаментальных констант, используемых во всей
 * системе в целом
 */

// Constants
define('HOST', 'mymoney');


// Session
ini_set('session.use_cookies', 1);
ini_set('session.use_trans_sid', 1);
session_start();

?>