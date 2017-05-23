<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app.php';

/** @var \Symfony\Component\HttpFoundation\Request $request */
$request = $app['request'];
$user = $_COOKIE['login'];
if (!$user) {
    $redirect = new \Symfony\Component\HttpFoundation\RedirectResponse('/login.php');
    $redirect->send();
    return;
}

/** @var \CartBooking\Application\Http\PlacementsController $controller */
$controller = $app[\CartBooking\Application\Http\PlacementsController::class];
if ($request->isMethod(\Symfony\Component\HttpFoundation\Request::METHOD_POST)) {
//    switch ($request->get('action')) {
//        case '':
//
//    }
    return $controller->submitAction()->send();
}
if ($request->get('bookingId') > 0) {
    return $controller->reportAction((int)$request->get('bookingId'))->send();
}
return $controller->indexAction($user)->send();
