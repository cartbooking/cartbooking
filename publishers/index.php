<?php
require_once APP_ROOT . '/vendor/autoload.php';
require_once APP_ROOT . '/app.php';

/** @var \Symfony\Component\HttpFoundation\Request $request */
$request = $app['request'];
/** @var \CartBooking\Application\Http\PublishersController $controller */
$controller = $app[\CartBooking\Application\Http\PublishersController::class];
switch ($request->get('action')) {
    case 'search':
        $controller->searchAction($request->get('name'))->send();
        break;
    case 'low_participation':
        $controller->lowParticipants()->send();
        break;
    default:
        $controller->indexAction()->send();
}

