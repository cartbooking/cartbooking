<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app.php';
define('APP_ROOT', __DIR__);
$userId = $_COOKIE['login'];
if (!$userId) {
    return (new \Symfony\Component\HttpFoundation\RedirectResponse('/login.php'))->send();
}
/** @var \CartBooking\Application\Http\BookingController $controller */
$controller = $app[\CartBooking\Application\Http\BookingController::class];
return $controller->indexAction($userId)->send();
