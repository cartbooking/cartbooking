<?php

namespace CartBooking\Application\Provider;

use Bigcommerce\Injector\InjectorServiceProvider;
use CartBooking\Application\Web\Admin\AccountController;
use CartBooking\Application\Web\Admin\CommunicationController;
use CartBooking\Application\Web\Admin\LocationController;
use CartBooking\Application\Web\Admin\PublishersController;
use CartBooking\Application\Web\Admin\ReportsController;
use CartBooking\Application\Web\Admin\StatisticsController;
use Pimple\Container;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AdminControllerProvider extends InjectorServiceProvider implements ControllerProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app
     * @return void
     */
    public function register(Container $app)
    {

    }

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     * @throws \Exception
     */
    public function connect(Application $app)
    {
        $controllers = $this->get(ControllerCollection::class);
        $controllers->get('/publishers/low-participation', function () {
            return $this->injector->create(PublishersController::class)->lowParticipants();
        });
        $controllers->post('/publishers/search', function (Request $request) {
            return $this->injector->create(PublishersController::class)->searchAction($request->get('name'));
        })->bind('admin/publishers/search');
        $controllers->match('/publishers/', function () {
            return $this->injector->create(PublishersController::class)->indexAction();
        })->bind('admin/publishers');
        $controllers->match('/publishers/{publisherId}', function ($publisherId) {
            return $this->injector->create(PublishersController::class)->editAction($publisherId);
        });

        $controllers->get('/communication/', function () {
            return $this->injector->create(CommunicationController::class)->indexAction();
        });
        $controllers->post('/communication/', function (Request $request) {
            $controller = $this->injector->create(CommunicationController::class);
            switch ($request->get('action')) {
                case 'placement_reminder':
                    return $controller->sendBookingReminderEmailsAction();
                case 'volunteer_needed':
                    return $controller->sendVolunteerNeededEmailsAction();
                case 'overseer_needed':
                    return $controller->sendOverseerNeededEmailsAction();
            }
        });
        $controllers->get('/statistics/', function () {
            return $this->injector->create(StatisticsController::class)->indexAction();
        })->bind('admin/statistics');
        $controllers->get('/reports', function () {
            return $this->injector->create(ReportsController::class)->indexAction();
        })->bind('admin/reports');
        $controllers->post('/reports', function (Request $request) {
            if ($request->get('action') === 'List Brothers') {
                return $this->injector->create(ReportsController::class)->listBrothersAction()->send();
            }
            if ($request->get('action') === 'List Invitees') {
                return $this->injector->create(ReportsController::class)->listInviteesAction()->send();
            }
            return new RedirectResponse('/');
        });
        $controllers->get('/locations', function () {
            return $this->injector->create(LocationController::class)->indexAction();
        })->bind('admin/locations');
        $controllers->match('/locations/{locationId}', function ($locationId) {
            return $this->injector->create(LocationController::class)->editAction($locationId);
        });
        $controllers->match('/account', function () {
            return $this->injector->create(AccountController::class)->indexAction();
        })->bind('admin/account');
        return $controllers;
    }
}
