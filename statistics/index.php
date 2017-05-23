<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app.php';

/** @var \CartBooking\Application\Http\StatisticsController $controller */
$controller = $app[\CartBooking\Application\Http\StatisticsController::class];
return $controller->indexAction()->send();
