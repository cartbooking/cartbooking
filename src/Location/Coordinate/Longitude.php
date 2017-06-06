<?php

namespace CartBooking\Location\Coordinate;

class Longitude
{
    /** @var float */
    private $longitude;

    public function __construct(float $longitude)
    {
        $this->longitude = $longitude;
    }

    public function __toString()
    {
        return (string)$this->longitude;
    }
}
