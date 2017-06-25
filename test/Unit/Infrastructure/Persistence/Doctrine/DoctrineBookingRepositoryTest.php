<?php

namespace Test\Unit\Infrastructure\Persistence\Doctrine;

use CartBooking\Model\Booking\Booking;
use CartBooking\Model\Booking\BookingId;
use CartBooking\Model\Booking\BookingRepository;
use Doctrine\ORM\EntityManager;
use Test\AutoMockingTest;

class DoctrineBookingRepositoryTest extends AutoMockingTest
{
    /** @var BookingRepository */
    private $bookingRepository;

    public function setUp()
    {
        parent::setUp();
        $this->bookingRepository = $this->injector->create(BookingRepository::class);
    }

    public function testFindById()
    {
        $id = new BookingId();
        $booking = $this->prophesize(Booking::class);
        $this->injector->getProphecy(EntityManager::class)->find(Booking::class, $id)
            ->willReturn($booking->reveal());
        static::assertSame($booking->reveal(), $this->bookingRepository->findById($id));
    }
}
