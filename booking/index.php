<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app.php';
$userId = $_COOKIE['login'];
if (!$userId) {
    return (new \Symfony\Component\HttpFoundation\RedirectResponse('/login.php'))->send();
}
/** @var \Symfony\Component\HttpFoundation\Request $request */
$request = $app['request'];
/** @var \CartBooking\Application\Http\BookingController $controller */
$controller = $app[\CartBooking\Application\Http\BookingController::class];
if ($request->isMethod(\Symfony\Component\HttpFoundation\Request::METHOD_POST)) {
    return $controller->postAction()->send();
}
return $controller->indexAction($userId)->send();
