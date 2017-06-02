<?php
require_once __DIR__ . '/vendor/autoload.php';

define('APP_ROOT', __DIR__);
$initParams = parse_ini_file(APP_ROOT . '/config.ini');
date_default_timezone_set($initParams['timezone']);
$app = new Silex\Application();
$app['initParams'] = $initParams;
$provider = new \CartBooking\Provider\CoreProvider($initParams);
$provider->register($app);
$provider->mount($app);
\CartBooking\Application\ServiceLocator::setContainer($app);
$app->boot();
$app->run();
