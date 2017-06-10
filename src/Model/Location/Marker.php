<?php

namespace CartBooking\Model\Location;

use CartBooking\Model\Location\Marker\Color;
use CartBooking\Model\Location\Marker\Label;

class Marker
{
    /** @var Color */
    private $color;
    /** @var Label */
    private $label;
    /** @var Coordinate */
    private $coordinates;

    public function __construct(Color $color, Label $label, Coordinate $coordinates)
    {
        $this->color = $color;
        $this->label = $label;
        $this->coordinates = $coordinates;
    }

    public function __toString()
    {
        return implode('|', [$this->color, $this->label, $this->coordinates]);
    }
}
