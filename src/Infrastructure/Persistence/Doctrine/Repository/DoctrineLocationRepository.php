<?php

namespace CartBooking\Infrastructure\Persistence\Doctrine\Repository;

use CartBooking\Location\Location;
use CartBooking\Location\LocationRepositoryInterface;
use Doctrine\ORM\EntityManager;

final class DoctrineLocationRepository implements LocationRepositoryInterface
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $manager)
    {
        $this->entityManager = $manager;
    }

    /**
     * @return \Generator|Location[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(Location::class)->findAll();
    }

    public function findById(int $id)
    {
        return $this->entityManager->find(Location::class, $id);
    }
}
