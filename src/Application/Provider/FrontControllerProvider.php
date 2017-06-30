<?php

namespace CartBooking\Application\Provider;

use Bigcommerce\Injector\InjectorServiceProvider;
use CartBooking\Application\Web\Front\BookingController;
use CartBooking\Application\Web\Front\ExperiencesController;
use CartBooking\Application\Web\Front\LocationsController;
use CartBooking\Application\Web\Front\PlacementsController;
use Pimple\Container;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class FrontControllerProvider extends InjectorServiceProvider implements ControllerProviderInterface
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
        $controllers->get('/', function (Application $app) {
            if ($app['security.token_storage']->getToken() === null) {
                return new RedirectResponse('/login');
            }
            return new RedirectResponse('/booking');
        });

        $controllers->get('/booking/', function () {
            return $this->injector->create(BookingController::class)->indexAction();
        })->bind('bookings');
        $controllers->post('/booking/', function () {
            return $this->injector->create(BookingController::class)->postAction();
        });
        $controllers->post('/placements/', function () {
            return $this->injector->create(PlacementsController::class)->postAction();
        });
        $controllers->get('/placements/{bookingId}', function ($bookingId) {
            return $this->injector->create(PlacementsController::class)->reportAction((int)$bookingId);
        })->assert('bookingId', '\d+');
        $controllers->get('/placements/', function () {
            return $this->injector->create(PlacementsController::class)->indexAction();
        })->bind('placements');

        $controllers->get('/experiences/', function () {
            return $this->injector->create(ExperiencesController::class)->indexAction();
        })->bind('experiences');
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
        })->bind('locations');


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
