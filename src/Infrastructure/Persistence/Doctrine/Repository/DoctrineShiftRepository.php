<?php

namespace CartBooking\Infrastructure\Persistence\Doctrine\Repository;

use CartBooking\Shift\Shift;
use CartBooking\Shift\ShiftRepositoryInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;

final class DoctrineShiftRepository implements ShiftRepositoryInterface
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     * @return Shift
     */
    public function findById(int $id)
    {
        return $this->entityManager->find(Shift::class, $id);
    }

    /**
     * @param int $day
     * @param int $locationId
     * @return Shift[]
     */
    public function findByDayAndLocation(int $day, int $locationId): array
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('day', $day))
            ->andWhere(Criteria::expr()->eq('locationId', $locationId));
        return iterator_to_array($this->entityManager->getRepository(Shift::class)->matching($criteria));
    }
}
