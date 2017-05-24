<?php

namespace CartBooking\Booking;

use CartBooking\Booking\Command\CreateBookingCommand;
use CartBooking\Publisher\PublisherRepository;

class BookingService
{
    /** @var BookingRepository */
    private $bookingRepository;
    /** @var PublisherRepository */
    private $publisherRepository;

    public function __construct(BookingRepository $bookingRepository, PublisherRepository $publisherRepository)
    {
        $this->bookingRepository = $bookingRepository;
        $this->publisherRepository = $publisherRepository;
    }

    public function createBooking(CreateBookingCommand $command)
    {
        return new Booking($this->bookingRepository->getNextId(), $command->getPublisherId(), $command->getShiftId(), $command->getDate());
    }
}
