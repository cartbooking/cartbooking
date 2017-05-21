<?php
require_once APP_ROOT . '/vendor/autoload.php';
require_once APP_ROOT . '/app.php';

/** @var \Symfony\Component\HttpFoundation\Request $request */
$request = $app['request'];
/** @var \CartBooking\Application\Http\CommunicationController $controller */
$controller = $app[\CartBooking\Application\Http\CommunicationController::class];
switch ($request->get('action')) {
    case 'placement_reminder':
        $controller->sendBookingReminderEmailsAction()->send();
        break;
    case 'volunteer_needed':
        $controller->sendVolunteerNeededEmailsAction()->send();
        break;
    case 'overseer_needed':
        $controller->sendOverseerNeededEmailsAction()->send();
        break;
    default:
        return $controller->indexAction()->send();
}
