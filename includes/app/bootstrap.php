<?php

require 'includes/vendor/autoload.php';

ini_set('display_errors', 'On');
ini_set('html_errors', 'On');
ini_set('xdebug.trace_output_name', 'calculate_example.%t');

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


$app_dir = dirname(__DIR__) . '/app/';
$settings = require $app_dir . 'settings.php';

$container = new \Slim\Container($settings);

require $app_dir . 'dependencies.php';

session_start();

$app = new \Slim\App($container);


require $app_dir . 'routes.php';

$app->run();