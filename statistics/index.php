<?php

require_once APP_ROOT . '/vendor/autoload.php';
require_once APP_ROOT . '/app.php';

/** @var \CartBooking\Application\Http\StatisticsController $controller */
$controller = $app[\CartBooking\Application\Http\StatisticsController::class];
return $controller->indexAction()->send();
