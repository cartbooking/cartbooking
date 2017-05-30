<?php

namespace CartBooking\Booking;

use CartBooking\Booking\Command\AddPublishersCommand;
use CartBooking\Booking\Command\CreateBookingCommand;
use CartBooking\Booking\Command\DeletePublisherFromBookingCommand;
use CartBooking\Booking\Exception\NotFoundException;
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

    public function getById(int $bookingId)
    {
        $booking =  $this->bookingRepository->findById($bookingId);
        if ($booking === null) {
            throw new NotFoundException();
        }
        return $booking;
    }

    public function createBooking(CreateBookingCommand $command)
    {
        $publishers = [];
        $booking = new Booking($this->bookingRepository->nextId(), $command->getShiftId(), $command->getDate());
        foreach ($command->getPublishersIds() as $publisherId) {
            $publisher = $this->publisherRepository->findById($publisherId);
            if ($publisher !== null) {
                $publishers[] = $publisher;
            }
        }
        $booking->setPublishers($publishers);
        $this->bookingRepository->save($booking);
        return $booking->getId();
    }

    public function addPublishers(AddPublishersCommand $command)
    {
        $booking = $this->bookingRepository->findById($command->getBookingId());
        if ($booking === null) {
            return;
        }
        $publishers = [];
        foreach (array_diff($command->getPublishersIds(), $booking->getPublishersIds()) as $publisherId) {
            $publisher = $this->publisherRepository->findById($publisherId);
            if ($publisher !== null) {
                $publishers[] = $publisher;
            }
        }
        foreach ($booking->getPublishersIds() as $publisherId) {
            $publisher = $this->publisherRepository->findById($publisherId);
            if ($publisher !== null) {
                $publishers[] = $publisher;
            }
        }
        $booking->setPublishers($publishers);
        $this->bookingRepository->save($booking);
    }

    public function removePublishers(DeletePublisherFromBookingCommand $command)
    {
        $publishers = [];
        $booking = $this->bookingRepository->findById($command->getBookingId());
        if ($booking === null) {
            return;
        }
        if (!in_array($command->getPublisherId(), $booking->getPublishersIds(), true)) {
            return;
        }

        foreach (array_unique(array_diff($booking->getPublishersIds(), [$command->getPublisherId()])) as $publisherId) {
            $publisher = $this->publisherRepository->findById($publisherId);
            if ($publisher !== null) {
                $publishers[] = $publisher;
            }
        }
        $booking->setPublishers($publishers);
        $this->bookingRepository->save($booking);
    }
}
