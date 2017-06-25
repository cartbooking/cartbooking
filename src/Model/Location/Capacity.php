<?php

namespace CartBooking\Model\Location;

class Capacity
{
    /** @var int */
    private $capacity;

    public function __construct(int $capacity)
    {
        if ($capacity < 1) {
            throw new \InvalidArgumentException('Capacity must be greater than zero');
        }
        $this->capacity = $capacity;
    }

    public function capacity()
    {
        return $this->capacity();
    }
}
