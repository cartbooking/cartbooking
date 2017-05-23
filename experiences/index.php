<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app.php';

/** @var \Symfony\Component\HttpFoundation\Request $request */
$request = $app['request'];
/** @var \CartBooking\Application\Http\ExperiencesController $controller */
$controller = $app[\CartBooking\Application\Http\ExperiencesController::class];
if ($request->isMethod(\Symfony\Component\HttpFoundation\Request::METHOD_POST)) {
    $controller->postAction((int)$request->get('dismissed'))->send();
    return;
}

$controller->indexAction()->send();
