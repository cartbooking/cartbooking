<?php

namespace CartBooking\Location;

class Location
{
    /** @var int */
    private $id;
    /** @var string */
    private $description;
    /** @var int */
    private $capacity = 0;
    /** @var string */
    private $centre;
    /** @var string[] */
    private $markers = [];
    /** @var string */
    private $name;
    /** @var string */
    private $path;
    /**  @var int */
    private $volunteers;
    /** @var int */
    private $zoom;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getVolunteers(): int
    {
        return $this->volunteers;
    }

    /**
     * @param int $volunteers
     */
    public function setVolunteers(int $volunteers)
    {
        $this->volunteers = $volunteers;
    }

    /**
     * @return string
     */
    public function getCentre(): string
    {
        return $this->centre;
    }

    /**
     * @param string $centre
     */
    public function setCentre(string $centre)
    {
        $this->centre = $centre;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string[]
     */
    public function getMarkers(): array
    {
        return $this->markers;
    }

    /**
     * @param string[] $markers
     */
    public function setMarkers(array $markers)
    {
        $this->markers = $markers;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getZoom(): int
    {
        return $this->zoom;
    }

    /**
     * @param int $zoom
     */
    public function setZoom(int $zoom)
    {
        $this->zoom = $zoom;
    }

    /**
     * @return int
     */
    public function getCapacity(): int
    {
        return $this->capacity;
    }

    /**
     * @param int $capacity
     */
    public function setCapacity(int $capacity)
    {
        $this->capacity = $capacity;
    }


}
