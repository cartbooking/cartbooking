<?php

namespace CartBooking\Shift;

interface ShiftRepositoryInterface
{
    /**
     * @param int $id
     * @return Shift
     */
    public function findById(int $id);

    /**
     * @param int $day
     * @param int $locationId
     * @return Shift[]
     */
    public function findByDayAndLocation(int $day, int $locationId): array;
}
