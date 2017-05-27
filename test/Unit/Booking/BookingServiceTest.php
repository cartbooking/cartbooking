<?php

namespace Test\Unit\Booking;

use CartBooking\Booking\Booking;
use CartBooking\Booking\BookingRepository;
use CartBooking\Booking\BookingService;
use CartBooking\Booking\Command\CreateBookingCommand;
use CartBooking\Publisher\Publisher;
use CartBooking\Publisher\PublisherRepository;
use Prophecy\Argument;
use Test\AutoMockingTest;

class BookingServiceTest extends AutoMockingTest
{
    /** @var BookingService */
    private $bookingService;

    public function setUp()
    {
        parent::setUp();
        $this->bookingService = $this->injector->create(BookingService::class);
    }

    public function testBookingService()
    {
        $publisherId = 1;
        $publisher = $this->prophesize(Publisher::class);
        $publisher->isMale()->willReturn(true);
        $publisher->getId()->willReturn($publisherId);
        $command = new CreateBookingCommand(1, '2017-01-01', [$publisherId]);
        $bookingId = 2;
        $this->injector->getProphecy(BookingRepository::class)->nextId()->willReturn($bookingId);
        $this->injector->getProphecy(PublisherRepository::class)->findById($publisherId)->willReturn($publisher->reveal());
        $this->injector->getProphecy(BookingRepository::class)->save(Argument::type(Booking::class))->shouldBeCalled();
        static::assertSame($bookingId, $this->bookingService->createBooking($command));
    }
}
