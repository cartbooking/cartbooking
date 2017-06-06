<?php
/**
 * Created by PhpStorm.
 * User: sebastian.machuca
 * Date: 30/4/17
 * Time: 10:23 PM
 */

namespace CartBooking\Location;


use Doctrine\ORM\EntityManager;

class LocationRepository
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
