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
        if ($publishersIds === []) {
            throw new InvalidArgumentException('Missing collection of publishers');
        }
        $publishersIds = array_map(function ($id) {
            if ((int) $id < 1) {
                throw new InvalidArgumentException('Invalid Publishers IDs');
            }
            return (int) $id;
            }, $publishersIds);
        if ($shiftId < 1) {
            throw new InvalidArgumentException('Invalid Shift ID');
        }
        $this->shiftId = $shiftId;
        $this->date = DateTimeImmutable::createFromFormat('Y-m-d|', $date);
        $this->publishersIds = array_unique($publishersIds);
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
