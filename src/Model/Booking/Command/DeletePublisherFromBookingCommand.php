<?php

namespace CartBooking\Model\Booking\Command;

use CartBooking\Model\Booking\BookingId;

class DeletePublisherFromBookingCommand
{
    /** @var BookingId */
    private $bookingId;
    /** @var int[] */
    private $publisherId;

    public function __construct(BookingId $bookingId, int $publisherId)
    {
        $this->bookingId = $bookingId;
        $this->publisherId = $publisherId;
    }

    /**
     * @return BookingId
     */
    public function getBookingId(): BookingId
    {
        return $this->bookingId;
    }

    /**
     * @return int
     */
    public function getPublisherId(): int
    {
        return $this->publisherId;
    }

}
