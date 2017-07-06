<?php

namespace CartBooking\Application\Provider;

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
        $this->autoBind(PublisherRepository::class);
        $this->autoBind(BookingRepository::class);
        $this->alias(ShiftRepositoryInterface::class, DoctrineShiftRepository::class);
        $this->autoBind(DoctrineShiftRepository::class);
        $this->alias(LocationRepositoryInterface::class, DoctrineLocationRepository::class);
        $this->autoBind(DoctrineLocationRepository::class);
    }
}
