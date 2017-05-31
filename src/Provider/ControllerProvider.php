<?php

namespace CartBooking\Provider;

use Bigcommerce\Injector\InjectorServiceProvider;
use CartBooking\Application\EmailService;
use CartBooking\Application\Http\BookingController;
use CartBooking\Application\Http\CommunicationController;
use CartBooking\Application\Http\ExperiencesController;
use CartBooking\Application\Http\MapsController;
use CartBooking\Application\Http\PlacementsController;
use CartBooking\Application\Http\PublishersController;
use CartBooking\Application\Http\ReportsController;
use CartBooking\Application\Http\StatisticsController;
use CartBooking\Booking\BookingService;
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
        $initParams = $app['initParams'];
        $app[MapsController::class] = function (Container $app) use ($initParams) {
            return new MapsController($app['request'], $app['response'], $app['repository.location'], $initParams, $app['twig']);
        };

        $app[StatisticsController::class] = function (Container $app) {
            return new StatisticsController($app['request'], $app['response'], $app['repository.booking'], $app['twig']);
        };
        $app[ExperiencesController::class] = function (Container $app) {
            return new ExperiencesController($app['request'], $app['response'], $app['twig'], $app['repository.booking'], $app['repository.pioneer']);
        };

        $app[PlacementsController::class] = function (Container $app) {
            return new PlacementsController(
                $app['request'],
                $app['response'],
                $app['twig'],
                $app['repository.booking'],
                $app['repository.location'],
                $app['repository.shift']
            );
        };

        $app[CommunicationController::class] = function (Container $app) {
            return new CommunicationController(
                $app[EmailService::class],
                $app['request'],
                $app['response'],
                $app['twig'],
                $app['repository.booking'],
                $app['repository.location'],
                $app['repository.pioneer'],
                $app['repository.shift']
            );
        };

        $app[PublishersController::class] = function (Container $app) {
            return new PublishersController($app['request'], $app['repository.booking'], $app['repository.pioneer'], $app['twig']);
        };

        $app[ReportsController::class] = function (Container $app) {
            return new ReportsController($app['request'], $app['response'], $app['twig'], $app['repository.pioneer'], $app[FileSystem::class]);
        };

        $app[BookingController::class] = function (Container $app) {
            return new BookingController(
                $app['request'],
                $app['repository.booking'],
                $app[BookingService::class],
                $app['repository.location'],
                $app['repository.pioneer'],
                $app['repository.shift'],
                $app['twig']
            );
        };
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

        $controllers->get('/booking/', function (Application $app) use ($userId) {
            return $this->get(BookingController::class)->indexAction($userId);
        });
        $controllers->post('/booking/', function (Application $app) {
            return $this->get(BookingController::class)->postAction();
        });
        $controllers->post('/placements/', function (Application $app) {
            return $this->get(PlacementsController::class)->submitAction();
        });
        $controllers->get('/placements/{bookingId}', function (Application $app, $bookingId) {
            return $this->get(PlacementsController::class)->reportAction((int)$bookingId);
        })->assert('bookingId', '\d+');
        $controllers->get('/placements/', function (Application $app) use ($userId) {
            return $this->get(PlacementsController::class)->indexAction($userId);
        });

        return $controllers;
    }
}
