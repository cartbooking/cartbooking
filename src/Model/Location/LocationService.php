<?php

namespace CartBooking\Model\Location;

use CartBooking\Model\Location\Command\UpdateLocationCommand;

class LocationService
{
    /** @var LocationRepositoryInterface */
    private $locationRepository;

    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function findById(int $locationId)
    {
        return $this->locationRepository->findById($locationId);
    }

    public function findAll()
    {
        return $this->locationRepository->findAll();
    }

    public function updateLocation(UpdateLocationCommand $command)
    {
        $location = $this->locationRepository->findById($command->getLocationId());
        $location->setCapacity($command->getCapacity()->capacity());
        $location->setDescription($command->getDescription());
        $location->setName($command->getName());
        $this->locationRepository->save($location);
    }

    public function save(Location $location)
    {
        return $this->locationRepository->save($location);
    }
}
