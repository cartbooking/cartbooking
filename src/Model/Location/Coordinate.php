<?php

namespace CartBooking\Model\Location;

use CartBooking\Model\Location\Coordinate\Latitude;
use CartBooking\Model\Location\Coordinate\Longitude;

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
