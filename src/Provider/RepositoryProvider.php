<?php

namespace CartBooking\Provider;

use Bigcommerce\Injector\InjectorServiceProvider;
use CartBooking\Infrastructure\Persistence\Doctrine\Repository\DoctrineLocationRepository;
use CartBooking\Infrastructure\Persistence\Doctrine\Repository\DoctrineShiftRepository;
use CartBooking\Model\Booking\BookingRepository;
use CartBooking\Model\Location\LocationRepositoryInterface;
use CartBooking\Model\Publisher\PublisherRepository;
use CartBooking\Model\Shift\ShiftRepositoryInterface;
use Pimple\Container;

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

        $this->autoBind(PublisherRepository::class);

        $this->bind(BookingRepository::class, function (Container $app) {
            return new BookingRepository($app[\CartBooking\Lib\Db\Db::class], new \CartBooking\Model\Booking\BookingHydrator());
        });
        $this->alias('repository.booking', BookingRepository::class);

        $this->alias(ShiftRepositoryInterface::class, DoctrineShiftRepository::class);
        $this->autoBind(DoctrineShiftRepository::class);
        $this->alias(LocationRepositoryInterface::class, DoctrineLocationRepository::class);
        $this->autoBind(DoctrineLocationRepository::class);
    }
}
