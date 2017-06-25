<?php

namespace CartBooking\Model\Location;

use InvalidArgumentException;

class Name
{
    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        if (strlen($name) < 1) {
            throw new InvalidArgumentException('Name must contain more than 1 character');
        }
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->name;
    }
}
