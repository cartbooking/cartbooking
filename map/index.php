<?php
require_once APP_ROOT . '/vendor/autoload.php';
require_once APP_ROOT . '/app.php';
$user = $_COOKIE['login'];
if (!$user) {
    header('Location:login.php');
    exit();
}
$publisher = \CartBooking\Application\ServiceLocator::getPioneerRepository()->findById($user);
$first_name = $publisher->getFirstName();
$last_name = $publisher->getLastName();
$gender = $publisher->getGender();
$phone = $publisher->getPhone();
$inactive = $publisher->isInactive();
if ($inactive === 'd') {
    header('Location:login.php');
    exit();
}

/** @var \Symfony\Component\HttpFoundation\Request $request */
$request = $app['request'];
/** @var \CartBooking\Application\Http\MapsController $controller */
$controller = $app[\CartBooking\Application\Http\MapsController::class];
if ($request->get('location') > 0) {
    return $controller->location();
}
$controller->allLocations();
