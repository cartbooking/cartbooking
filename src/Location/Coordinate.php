<?php

namespace CartBooking\Location;

use CartBooking\Location\Coordinate\Latitude;
use CartBooking\Location\Coordinate\Longitude;

class Coordinate
{
    /** @var Latitude */
    private $latitude;
    /** @var Longitude */
    private $longitude;

    public function __construct(Latitude $latitude, Longitude $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function __toString()
    {
        return implode(',', [$this->latitude, $this->longitude]);
    }
}
