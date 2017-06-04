<?php

namespace CartBooking\Location\Marker;

class Coordinate
{
    /** @var float */
    private $latitude;
    /** @var float */
    private $longitude;

    public function __construct(float $latitude, float $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function __toString()
    {
        return implode(',', [$this->latitude, $this->longitude]);
    }
}
