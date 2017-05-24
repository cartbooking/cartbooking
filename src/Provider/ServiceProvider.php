<?php

namespace CartBooking\Provider;

use Bigcommerce\Injector\InjectorServiceProvider;
use CartBooking\Booking\BookingService;
use CartBooking\Lib\Utilities\FileSystem;
use Pimple\Container;
use Silex\Provider\SessionServiceProvider;

class ServiceProvider extends InjectorServiceProvider
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

        $app[SessionServiceProvider::class] = function (Container $app) {
            $sessionServiceProvider = new SessionServiceProvider();
            $sessionServiceProvider->register($app);
            return $sessionServiceProvider;
        };

        $app[FileSystem::class] = function () use ($initParams) {
            return new FileSystem($initParams);
        };
    }
}
