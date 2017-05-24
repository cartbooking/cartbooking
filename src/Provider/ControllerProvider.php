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

class ControllerProvider extends InjectorServiceProvider
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
}
