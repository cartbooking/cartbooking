<?php

namespace CartBooking\Model\Location\Marker;

class Color
{
    /** @var string */
    private $color;

    public function __construct(string $color)
    {
        $this->color = $color;
    }

    public function __toString()
    {
        return 'color:' . $this->color;
    }
}
