<?php
//error settings
ini_set("display_errors", "Off");
error_reporting(0);
ini_set("log_errors", 1);
ini_set("error_log", dirname(__FILE__) . "/error_log.txt");

//include db
require(dirname(__FILE__) . "/DB.php");

//global settings
define('ROOT', '/absolute/path/to/rearend/');
define('WEBROOT', 'http://domain/path/to/rearend/');

define('LIB_ROOT', ROOT . 'lib/');
define('APPS_ROOT', ROOT . 'apps/');
define('CONFIG_ROOT', ROOT . 'config/');

define('MODELS_ROOT', LIB_ROOT . 'Models/');
define('DAOS_ROOT', LIB_ROOT . 'MYSQL-DAOS/');

//timezone
date_default_timezone_set("Europe/Amsterdam");

//internal images paths
define('UPLOADS_PATH', ROOT . 'uploads/originals/');
define('THUMBS_PATH', ROOT . 'uploads/thumbnails/');

//http images paths
define('UPLOADS', WEBROOT . 'uploads/originals/');
define('THUMBS', WEBROOT . 'uploads/thumbnails/');

//maximum picture upload pixel sizes
define('MAX_UPLOADS_PX', 900);
define('MAX_THUMBS_PX', 300);

//shared key
define('SHARED_KEY', 'demo');
?>
