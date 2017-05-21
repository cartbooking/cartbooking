<?php

namespace CartBooking\Shift;

class ShiftHydrator
{
    public function hydrate(array $row)
    {
        return new Shift($row['id'], $row['location_id'], $row['day'], $row['start_time'], $row['end_time']);

    }
}
