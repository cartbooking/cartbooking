<?php

namespace CartBooking\Provider;

use Bigcommerce\Injector\InjectorServiceProvider;
use CartBooking\Booking\BookingRepository;
use CartBooking\Location\LocationRepository;
use CartBooking\Publisher\PublisherRepository;
use CartBooking\Shift\ShiftRepository;
use Doctrine\DBAL\Connection;
use Pimple\Container;
use Silex\Provider\DoctrineServiceProvider;

class RepositoryProvider extends InjectorServiceProvider
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

        $app[\CartBooking\Lib\Db\Db::class] = function () use ($initParams) {
            return new \CartBooking\Lib\Db\Db(
                new \CartBooking\Lib\Db\Host($initParams['db']['host']),
                new \CartBooking\Lib\Db\Name($initParams['db']['name']),
                new \CartBooking\Lib\Db\Username($initParams['db']['username']),
                new \CartBooking\Lib\Db\Password($initParams['db']['password'])
            );
        };

        $this->bind(PublisherRepository::class, function (Container $app) {
            return new PublisherRepository($app[\CartBooking\Lib\Db\Db::class], new \CartBooking\Publisher\PublisherHydrator());
        });
        $this->alias('repository.pioneer', PublisherRepository::class);

        $this->bind(BookingRepository::class, function (Container $app) {
            return new BookingRepository($app[\CartBooking\Lib\Db\Db::class], new \CartBooking\Booking\BookingHydrator());
        });
        $this->alias('repository.booking', BookingRepository::class);
        $this->bind(ShiftRepository::class, function (Container $app) {
            return new ShiftRepository($app[\CartBooking\Lib\Db\Db::class], new \CartBooking\Shift\ShiftHydrator());
        });
        $this->alias('repository.shift', ShiftRepository::class);

        $this->bind(LocationRepository::class, function (Container $app) {
            return new LocationRepository($app[\CartBooking\Lib\Db\Db::class], new \CartBooking\Location\LocationHydrator());
        });
        $this->alias('repository.location', LocationRepository::class);
    }
}
