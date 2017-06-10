<?php

namespace Test\Unit\Booking;

use CartBooking\Model\Booking\Booking;
use CartBooking\Model\Booking\BookingRepository;
use CartBooking\Model\Booking\BookingService;
use CartBooking\Model\Booking\Command\AddPublishersCommand;
use CartBooking\Model\Booking\Command\CreateBookingCommand;
use CartBooking\Model\Booking\Command\DeletePublisherFromBookingCommand;
use CartBooking\Model\Booking\Exception\NotFoundException;
use CartBooking\Model\Publisher\Publisher;
use CartBooking\Model\Publisher\PublisherRepository;
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

    public function testAddNoPublisher()
    {
        $bookingId = 123;
        $publishersIds = [1];
        $command = new AddPublishersCommand($bookingId, $publishersIds);
        $booking = $this->prophesize(Booking::class);
        $booking->getPublishersIds()->willReturn([2]);
        $booking->setPublishers([])->shouldBeCalled();
        $this->injector->getProphecy(BookingRepository::class)->findById($bookingId)->willReturn($booking->reveal());
        $this->injector->getProphecy(BookingRepository::class)->save($booking->reveal())->shouldBeCalled();
        $this->injector->getProphecy(PublisherRepository::class)->findById(1)->willReturn(null);
        $this->injector->getProphecy(PublisherRepository::class)->findById(2)->willReturn(null);
        $this->bookingService->addPublishers($command);
    }

    public function testAddPublisher()
    {
        $bookingId = 123;
        $overseerId = 1;
        $publisherId = 2;
        $overseer = $this->prophesize(Publisher::class);
        $publisher = $this->prophesize(Publisher::class);
        $command = new AddPublishersCommand($bookingId, [$publisherId]);
        $booking = $this->prophesize(Booking::class);
        $booking->getPublishersIds()->willReturn([$overseerId]);
        $booking->setPublishers(Argument::size(2))->shouldBeCalled();
        $this->injector->getProphecy(BookingRepository::class)->findById($bookingId)->willReturn($booking->reveal());
        $this->injector->getProphecy(BookingRepository::class)->save($booking->reveal())->shouldBeCalled();
        $this->injector->getProphecy(PublisherRepository::class)->findById($overseerId)->willReturn($overseer->reveal());
        $this->injector->getProphecy(PublisherRepository::class)->findById($publisherId)->willReturn($publisher->reveal());
        $this->bookingService->addPublishers($command);
    }

    public function testAddPublisherDoesNothingIfNotBooking()
    {
        $bookingId = 1;
        $this->injector->getProphecy(BookingRepository::class)->findById($bookingId)->willReturn(null);
        static::assertEmpty($this->bookingService->addPublishers(new AddPublishersCommand($bookingId, [1])));
    }

    public function testRemovePublisherSimple()
    {
        $bookingId = 1;
        $publisherId = 1;
        $this->injector->getProphecy(BookingRepository::class)->findById($bookingId)->shouldBeCalled();
        static::assertEmpty($this->bookingService->removePublishers(
            new DeletePublisherFromBookingCommand($bookingId, $publisherId)
        ));
    }

    public function testRemovingPublisherNonInBooking()
    {
        $bookingId = 1;
        $publisherId = 1;
        $booking = $this->prophesize(Booking::class);
        $this->injector->getProphecy(BookingRepository::class)->findById($bookingId)->willReturn($booking->reveal());
        $booking->getPublishersIds()->willReturn([]);
        static::assertEmpty(
            $this->bookingService->removePublishers(new DeletePublisherFromBookingCommand($bookingId, $publisherId))
        );
    }

    public function testRemoveAPublisherFromBooking()
    {
        $overseerId = 1;
        $publisherId = 2;
        $overseer = $this->prophesize(Publisher::class);
        $publisher = $this->prophesize(Publisher::class);
        $bookingId = 1;
        $booking = $this->prophesize(Booking::class);
        $booking->getPublishersIds()->willReturn([$overseerId, $publisherId]);
        $booking->setPublishers([$overseer->reveal()])->shouldBeCalled();
        $this->injector->getProphecy(BookingRepository::class)->findById($bookingId)->willReturn($booking->reveal());
        $this->injector->getProphecy(PublisherRepository::class)->findById($overseerId)->willReturn($overseer->reveal());
        $this->injector->getProphecy(PublisherRepository::class)->findById($publisherId)->willReturn($publisher->reveal());
        $this->injector->getProphecy(BookingRepository::class)->save($booking->reveal())->shouldBeCalled();
        $this->bookingService->removePublishers(new DeletePublisherFromBookingCommand($bookingId, $publisherId));
    }

    public function testGetByIdThrowsException()
    {
        $bookingId = 1;
        $this->injector->getProphecy(BookingRepository::class)->findById($bookingId)->willReturn(null);
        $this->expectException(NotFoundException::class);
        $this->bookingService->getById($bookingId);
    }

    public function testGetById()
    {
        $bookingId = 1;
        $booking = $this->prophesize(Booking::class);
        $this->injector->getProphecy(BookingRepository::class)->findById($bookingId)->willReturn($booking->reveal());
        static::assertSame($booking->reveal(), $this->bookingService->getById($bookingId));
    }
}
