<?php

namespace CartBooking\Booking\Command;

use CartBooking\Booking\Exception\InvalidArgumentException;
use DateTimeImmutable;

class CreateBookingCommand
{
    /** @var int */
    private $shiftId;
    /** @var DateTimeImmutable */
    private $date;
    /** @var array */
    private $publishersIds;

    public function __construct(int $shiftId, string $date, array $publishersIds)
    {
        $publishersIds = array_map(function ($id) {
            if ((int) $id < 1) {
                throw new InvalidArgumentException();
            }
            return (int) $id;
            }, $publishersIds);
        if ($shiftId < 1) {
            throw new InvalidArgumentException();
        }
        $this->shiftId = $shiftId;
        $this->date = DateTimeImmutable::createFromFormat('Y-m-d|', $date);
        $this->publishersIds = $publishersIds;
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

    /**
     * @return array
     */
    public function getPublishersIds(): array
    {
        return $this->publishersIds;
    }
}
