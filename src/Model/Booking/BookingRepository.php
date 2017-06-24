<?php
/**
 * Created by PhpStorm.
 * User: sebastian.machuca
 * Date: 30/4/17
 * Time: 8:43 PM
 */

namespace CartBooking\Model\Booking;


use CartBooking\Model\Publisher\Publisher;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;

class BookingRepository
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     * @return Booking|null
     */
    public function findById(int $id)
    {
        return $this->entityManager->find(Booking::class, $id);
    }

    public function findByPublisherId(int $publisherId)
    {
        $bookings = [];
        /** @var Booking $booking */
        foreach ($this->entityManager->getRepository(Booking::class)->findAll() as $booking) {
            foreach ($booking->getPublishers() as $publisher) {
                if ($publisher->getId() === $publisherId) {
                    $bookings[] = $booking;
                }
            }
        }
        return $bookings;
    }

    /**
     * @param DateTimeImmutable $date
     * @return \Generator|Booking[]
     */
    public function findNonRecordedBookingsOlderThan(DateTimeImmutable $date)
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->lte('date', $date));
        $criteria->andWhere(Criteria::expr()->eq('confirmed', true));
        $criteria->andWhere(Criteria::expr()->eq('recorded', false));
        return $this->entityManager->getRepository(Booking::class)->matching($criteria);
    }

    /**
     * @param int $userId
     * @param DateTimeImmutable $date
     * @return Booking[]
     */
    public function findPendingBookingsForUser(int $userId, DateTimeImmutable $date)
    {
        $query = $this->entityManager->createQuery(
            '
                    SELECT b FROM '.Booking::class.' b JOIN '.Publisher::class.' p WITH p.id = :publisherId
                    WHERE b.recorded = :recorded AND b.confirmed = :confirmed AND b.date <= :date 
                '
        )->setParameters([
            'publisherId' => $userId,
            'recorded' => false,
            'confirmed' => true,
            'date' => $date
        ]);
        return $query->getResult();
    }

    /**
     * @param int $publisherId
     * @param DateTimeInterface $date
     * @return Booking[]
     */
    public function findByPublisherIdAndDate(int $publisherId, DateTimeInterface $date): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT b FROM '.Booking::class.' b JOIN '.Publisher::class.' p WITH p.id = :publisherId WHERE b.date = :date '
        )->setParameters(['publisherId' => $publisherId, 'date' => $date]);
        return $query->getResult();
    }

    /**
     * @param int $shiftId
     * @param DateTimeInterface $date
     * @return Booking
     */
    public function findByShiftAndDate(int $shiftId, DateTimeInterface $date)
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('shiftId', $shiftId));
        $criteria->andWhere(Criteria::expr()->eq('date', $date));
        return $this->entityManager->getRepository(Booking::class)->matching($criteria)->first();
    }

    /**
     * @param DateTimeImmutable $fromDate
     * @param DateTimeImmutable $toDate
     * @return Collection|Booking[]
     */
    public function findByDateBetween(DateTimeImmutable $fromDate, DateTimeImmutable $toDate)
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->gte('date', $fromDate));
        $criteria->andWhere(Criteria::expr()->lte('date', $toDate));
        return $this->entityManager->getRepository(Booking::class)->matching($criteria);
    }

    /**
     * @return \Generator|Booking[]
     */
    public function findUnseenBookingsComments()
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->neq('comments', ''));
        $criteria->andWhere(Criteria::expr()->eq('seen', false));
        return $this->entityManager->getRepository(Booking::class)->matching($criteria);
    }

    public function save(Booking $booking)
    {
        $this->entityManager->persist($booking);
        $this->entityManager->flush();
    }

    /**
     * @param DateTimeImmutable $fromDate
     * @param DateTimeImmutable $toDate
     * @return \Generator|Booking[]
     */
    public function findBookingsNeedingOverseerBetween(DateTimeImmutable $fromDate, DateTimeImmutable $toDate)
    {
        return [];
    }

    /**
     * @param DateTimeImmutable $fromDate
     * @param DateTimeImmutable $toDate
     * @return \Generator|Booking[]
     */
    public function findBookingsNeedingVolunteersBetween(DateTimeImmutable $fromDate, DateTimeImmutable $toDate)
    {
        return [];
    }
}
