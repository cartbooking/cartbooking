<?php
require_once APP_ROOT . '/vendor/autoload.php';
require_once APP_ROOT . '/app.php';

/** @var \Symfony\Component\HttpFoundation\Request $request */
$request = $app['request'];
/** @var \CartBooking\Application\Http\ReportsController $controller */
$controller = $app[\CartBooking\Application\Http\ReportsController::class];

if ($request->isMethod(\Symfony\Component\HttpFoundation\Request::METHOD_POST)) {
    if ($request->get('action') === 'List Brothers') {
        return $controller->listBrothersAction()->send();
    }
    if ($request->get('action') === 'List Invitees') {
        return $controller->listInviteesAction()->send();
    }
}
$controller->indexAction()->send();
