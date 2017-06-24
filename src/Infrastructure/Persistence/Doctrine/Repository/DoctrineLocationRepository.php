<?php

namespace CartBooking\Infrastructure\Persistence\Doctrine\Repository;

use CartBooking\Model\Location\Location;
use CartBooking\Model\Location\LocationRepositoryInterface;
use CartBooking\Model\Shift\Shift;
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
     * @return Location[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(Location::class)->findAll();
    }

    /**
     * @param int $id
     * @return Location|null
     */
    public function findById(int $id)
    {
        return $this->entityManager->find(Location::class, $id);
    }

    public function findCombined()
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select()->from();

        $dql = 'SELECT l FROM ' . Location::class . ' l JOIN ' .Shift::class. ' s WITH l.id = s.locationId WHERE l.name = :name';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter(':name', 'Wentworth Point');
        $t = $query->getSQL();
        return $query->getResult();
    }
}
