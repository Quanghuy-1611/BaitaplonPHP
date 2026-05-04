<?php
session_start();

define('ROOT_PATH', __DIR__);
define('BASE_URL', '/BaitaplonPHP/');

require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/helpers/functions.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/App.php';

$app = new App();
$app->run();
