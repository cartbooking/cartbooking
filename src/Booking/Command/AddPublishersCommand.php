<?php

namespace CartBooking\Booking\Command;

class AddPublishersCommand
{
    /** @var int */
    private $bookingId;
    /** @var array */
    private $publishersIds;

    public function __construct(int $bookingId, array $publishersIds)
    {
        $this->bookingId = $bookingId;
        $this->publishersIds = $publishersIds;
    }

    /**
     * @return int
     */
    public function getBookingId(): int
    {
        return $this->bookingId;
    }

    /**
     * @return array
     */
    public function getPublishersIds(): array
    {
        return $this->publishersIds;
    }


}
