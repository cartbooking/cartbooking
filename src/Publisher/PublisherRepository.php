<?php

namespace CartBooking\Publisher;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;

class PublisherRepository
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $gender
     * @return Publisher[]
     */
    public function findByGender(string $gender)
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('gender', $gender));
        return $this->entityManager->getRepository(Publisher::class)->matching($criteria);
    }

    /**
     * @return Publisher[]|ArrayCollection
     */
    public function findAll(): ArrayCollection
    {
        return new ArrayCollection($this->entityManager->getRepository(Publisher::class)->findAll());
    }

    /**
     * @param int $id
     * @return \CartBooking\Publisher\Publisher|null
     */
    public function findById(int $id)
    {
        return $this->entityManager->find(Publisher::class, $id);
    }

    /**
     * @param string $phone
     * @return Publisher|null
     */
    public function findByPhone(string $phone)
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('phone', $phone));
        return $this->entityManager->getRepository(Publisher::class)->matching($criteria)->first();
    }

    /**
     * @param $name
     * @return Publisher[]
     */
    public function findByName($name)
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('firstName', $name))
            ->orWhere(Criteria::expr()->eq('lastName', $name));
        return $this->entityManager->getRepository(Publisher::class)->matching($criteria)->first();
    }

    /**
     * @return Publisher[]|\Generator
     */
    public function findActive()
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('inactive', 'y'))
//            ->andWhere(Criteria::expr()->eq('deactivated', 'd'))
            ->andWhere(Criteria::expr()->eq('email', ''));
        return $this->entityManager->getRepository(Publisher::class)->matching($criteria)->first();
    }
}
