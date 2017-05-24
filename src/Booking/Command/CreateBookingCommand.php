<?php

namespace CartBooking\Booking\Command;

use DateTimeImmutable;

class CreateBookingCommand
{
    /** @var int */
    private $publisherId;
    /** @var int */
    private $shiftId;
    /** @var DateTimeImmutable */
    private $date;

    public function __construct(int $publisherId, int $shiftId, string $date)
    {
        $this->publisherId = $publisherId;
        $this->shiftId = $shiftId;
        $this->date = DateTimeImmutable::createFromFormat('Y-m-d|', $date);
    }

    /**
     * @return int
     */
    public function getPublisherId(): int
    {
        return $this->publisherId;
    }

    /**
     * @return int
     */
    public function getShiftId(): int
    {
        return $this->shiftId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

}
