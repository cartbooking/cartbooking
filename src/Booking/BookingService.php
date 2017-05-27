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
        $publishers = [];
        $booking = new Booking($this->bookingRepository->nextId(), $command->getShiftId(), $command->getDate());
        foreach ($command->getPublishersIds() as $publishersId) {
            $publishers[] = $this->publisherRepository->findById($publishersId);
        }
        $booking->setPublishers($publishers);
        $this->bookingRepository->save($booking);
        return $booking->getId();
    }
}
