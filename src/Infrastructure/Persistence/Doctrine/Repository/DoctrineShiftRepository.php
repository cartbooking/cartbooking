<?php

namespace CartBooking\Infrastructure\Persistence\Doctrine\Repository;

use CartBooking\Model\Shift\Shift;
use CartBooking\Model\Shift\ShiftRepositoryInterface;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
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
     * @param DateTimeImmutable $day
     * @param int $locationId
     * @return Collection|Shift[]
     */
    public function findByDayAndLocation(DateTimeImmutable $day, int $locationId): Collection
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('day', $day->format('w')))
            ->andWhere(Criteria::expr()->eq('locationId', $locationId));
        return $this->entityManager->getRepository(Shift::class)->matching($criteria);
    }
}
