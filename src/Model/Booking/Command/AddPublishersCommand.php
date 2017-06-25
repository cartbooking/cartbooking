<?php

namespace CartBooking\Model\Booking\Command;

use CartBooking\Model\Booking\BookingId;

class AddPublishersCommand
{
    /** @var BookingId */
    private $bookingId;
    /** @var array */
    private $publishersIds;

    public function __construct(BookingId $bookingId, array $publishersIds)
    {
        $this->bookingId = $bookingId;
        $this->publishersIds = $publishersIds;
    }

    /**
     * @return BookingId
     */
    public function getBookingId(): BookingId
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
