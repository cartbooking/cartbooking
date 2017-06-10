<?php

namespace CartBooking\Model\Booking\Command;

class DeletePublisherFromBookingCommand
{
    /** @var int */
    private $bookingId;
    /** @var int[] */
    private $publisherId;

    public function __construct(int $bookingId, int $publisherId)
    {
        $this->bookingId = $bookingId;
        $this->publisherId = $publisherId;
    }

    /**
     * @return int
     */
    public function getBookingId(): int
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
