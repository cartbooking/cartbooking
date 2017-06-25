<?php

namespace CartBooking\Model\Location\Command;

use CartBooking\Model\Location\Capacity;
use CartBooking\Model\Location\Description;
use CartBooking\Model\Location\Name;

class UpdateLocationCommand
{
    /** @var int */
    private $locationId;
    /** @var Name */
    private $name;
    /** @var Description */
    private $description;
    /** @var Capacity */
    private $capacity;

    public function __construct(int $locationId, Name $name, Description $description, Capacity $capacity)
    {
        $this->locationId = $locationId;
        $this->name = $name;
        $this->description = $description;
        $this->capacity = $capacity;
    }

    /**
     * @return int
     */
    public function getLocationId(): int
    {
        return $this->locationId;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @return Description
     */
    public function getDescription(): Description
    {
        return $this->description;
    }

    /**
     * @return Capacity
     */
    public function getCapacity(): Capacity
    {
        return $this->capacity;
    }
}
