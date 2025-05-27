<?php
namespace MVC;

use MVC\core\App;

define('DS', DIRECTORY_SEPARATOR);
define('APP_PATH', realpath(dirname(__FILE__)) . DS. '..' . DS . 'src' . DS );
require_once APP_PATH . 'core' . DS . 'AutoLoader.php';
require_once APP_PATH . 'core' . DS . 'config' . DS . 'Helpers.php';

(new App(APP_PATH))->run();