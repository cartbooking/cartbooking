<?php

namespace CartBooking\Provider;

use Bigcommerce\Injector\InjectorServiceProvider;
use CartBooking\Application\Http\BookingController;
use CartBooking\Application\Http\CommunicationController;
use CartBooking\Application\Http\ExperiencesController;
use CartBooking\Application\Http\LocationsController;
use CartBooking\Application\Http\PlacementsController;
use CartBooking\Application\Http\PublishersController;
use CartBooking\Application\Http\ReportsController;
use CartBooking\Application\Http\StatisticsController;
use Pimple\Container;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ControllerProvider extends InjectorServiceProvider implements ControllerProviderInterface
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
     */
    public function connect(Application $app)
    {
        $controllers = $this->get(ControllerCollection::class);
        $userId = (int)$this->get(Request::class)->cookies->get('login');
        $controllers->get('/', function (Application $app) use($userId) {
            if (!$userId) {
                return new RedirectResponse('/login.php');
            }
            return new RedirectResponse('/booking');
        });

        $controllers->get('/booking/', function () use ($userId) {
            return $this->injector->create(BookingController::class)->indexAction($userId);
        });
        $controllers->post('/booking/', function () {
            return $this->injector->create(BookingController::class)->postAction();
        });
        $controllers->post('/placements/', function () {
            return $this->injector->create(PlacementsController::class)->postAction();
        });
        $controllers->get('/placements/{bookingId}', function ($bookingId) {
            return $this->injector->create(PlacementsController::class)->reportAction((int)$bookingId);
        })->assert('bookingId', '\d+');
        $controllers->get('/placements/', function () use ($userId) {
            return $this->injector->create(PlacementsController::class)->indexAction($userId);
        })->bind('/placements');
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
        $controllers->get('/experiences/', function () {
            return $this->injector->create(ExperiencesController::class)->indexAction();
        });
        $controllers->post('/experiences/', function (Request $request) {
            return $this->injector->create(ExperiencesController::class)->postAction((int)$request->get('dismissed'));
        });
        $controllers->get('/locations/{locationId}', function ($locationId) {
            return $this->injector->create(
                LocationsController::class,
                ['settings' => $this->get('initParams')]
            )->locationAction($locationId);
        });
        $controllers->get('/locations/', function () {
            return $this->injector->create(
                LocationsController::class,
                ['settings' => $this->get('initParams')]
            )->indexAction();
        })->bind('/locations');
        $controllers->get('/publishers/low-participation', function () {
            return $this->injector->create(PublishersController::class)->lowParticipants();
        });
        $controllers->get('/publishers/', function () {
            return $this->injector->create(PublishersController::class)->indexAction();
        });
        $controllers->post('/publishers/', function (Request $request) {
            return $this->injector->create(PublishersController::class)->searchAction($request->get('name'));
        });
        $controllers->get('/statistics/', function () {
            return $this->injector->create(StatisticsController::class)->indexAction();
        });
        $controllers->get('/reports', function () {
            return $this->injector->create(ReportsController::class)->indexAction();
        });
        $controllers->post('/reports', function (Request $request) {
            if ($request->get('action') === 'List Brothers') {
                return $this->injector->create(ReportsController::class)->listBrothersAction()->send();
            }
            if ($request->get('action') === 'List Invitees') {
                return $this->injector->create(ReportsController::class)->listInviteesAction()->send();
            }
            return new RedirectResponse('/');
        });
        $app->get('/login', function(Request $request) use ($app) {
            return $app['twig']->render('login.twig', [
                'error'         => $app['security.last_error']($request),
                'last_username' => $app['session']->get('_security.last_username'),
                'title' => 'Log in'
            ]);
        });

        return $controllers;
    }
}
