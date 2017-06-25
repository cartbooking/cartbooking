<?php

namespace CartBooking\Application\Provider;

use Bigcommerce\Injector\InjectorServiceProvider;
use CartBooking\Application\WebPublisherService;
use CartBooking\Lib\Utilities\FileSystem;
use CartBooking\Model\Booking\BookingService;
use CartBooking\Model\Location\LocationService;
use CartBooking\Model\Publisher\PublisherService;
use Pimple\Container;

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
     * @throws \Exception
     */
    public function register(Container $app)
    {
        $initParams = $app['initParams'];

        $app[FileSystem::class] = function () use ($initParams) {
            return new FileSystem($initParams);
        };

        $this->autoBind(BookingService::class);
        $this->autoBind(LocationService::class);
        $this->autoBind(PublisherService::class);
        $this->autoBind(WebPublisherService::class);
    }
}
