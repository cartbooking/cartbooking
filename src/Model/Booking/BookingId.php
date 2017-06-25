<?php

namespace CartBooking\Model\Booking;

use Ramsey\Uuid\Uuid;

class BookingId
{
    /** @var string */
    private $uuid;

    public function __construct(string $uuid = '')
    {
        $this->uuid = $uuid !== '' ? $uuid : Uuid::uuid4()->toString();
    }

    public function __toString()
    {
        return (string)$this->uuid;
    }
}
