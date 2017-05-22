<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;

$initParams = parse_ini_file(__DIR__ . '/config.ini');

date_default_timezone_set($initParams['timezone']);

$app = new \Pimple\Container();
$provider = new \CartBooking\Provider\CoreProvider($initParams);
$provider->register($app);

\CartBooking\Application\ServiceLocator::setContainer($app);
