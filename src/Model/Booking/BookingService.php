<?php

namespace CartBooking\Model\Booking;

use CartBooking\Model\Booking\Command\AddPublishersCommand;
use CartBooking\Model\Booking\Command\CreateBookingCommand;
use CartBooking\Model\Booking\Command\DeletePublisherFromBookingCommand;
use CartBooking\Model\Booking\Exception\NotFoundException;
use CartBooking\Model\Publisher\PublisherRepository;

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

    public function findById(BookingId $bookingId)
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
        $booking = new Booking(new BookingId(), $command->getShiftId(), $command->getDate());
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
