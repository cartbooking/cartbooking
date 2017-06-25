<?php

namespace CartBooking\Model\Location;

class Description
{
    /** @var string */
    private $description;

    public function __construct(string $description)
    {
        $this->description = $description;
    }

    public function __toString()
    {
        return $this->description;
    }
}
