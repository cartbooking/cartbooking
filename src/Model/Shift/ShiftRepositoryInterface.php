<?php

namespace CartBooking\Model\Shift;

use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;

interface ShiftRepositoryInterface
{
    /**
     * @param int $id
     * @return Shift
     */
    public function findById(int $id);

    /**
     * @param DateTimeImmutable $day
     * @param int $locationId
     * @return Collection|Shift[]
     */
    public function findByDayAndLocation(DateTimeImmutable $day, int $locationId): Collection;
}
