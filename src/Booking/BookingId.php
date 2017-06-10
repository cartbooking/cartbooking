<?php

namespace CartBooking\Booking;

use Ramsey\Uuid\Uuid;

class BookingId
{
    /** @var Uuid */
    private $uuid;

    public function __construct(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    public function __toString()
    {
        return (string)$this->uuid;
    }
}
