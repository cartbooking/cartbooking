<?php

namespace CartBooking\Model\Location\Coordinate;

class Latitude
{
    /** @var float */
    private $latitude;

    public function __construct(float $latitude)
    {
        $this->latitude = $latitude;
    }

    public function __toString()
    {
        return (string)$this->latitude;
    }
}
