<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
require_once __DIR__ . '/constants.php';
$initParams = parse_ini_file(__DIR__ . '/config.ini');
date_default_timezone_set($initParams['timezone']);

$app = new Silex\Application();
$app['initParams'] = $initParams;
$provider = new \CartBooking\Provider\CoreProvider($initParams);
$provider->register($app);
$provider->mount($app);
\CartBooking\Application\ServiceLocator::setContainer($app);
