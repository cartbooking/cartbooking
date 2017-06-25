<?php

namespace Test\Unit\Location;

use CartBooking\Model\Location\Capacity;
use CartBooking\Model\Location\Command\UpdateLocationCommand;
use CartBooking\Model\Location\Description;
use CartBooking\Model\Location\Location;
use CartBooking\Model\Location\LocationRepositoryInterface;
use CartBooking\Model\Location\LocationService;
use CartBooking\Model\Location\Name;
use Test\AutoMockingTest;

class LocationServiceTest extends AutoMockingTest
{
    /** @var LocationService */
    private $locationService;

    public function setUp()
    {
        parent::setUp();
        $this->locationService = $this->injector->create(LocationService::class);
    }

    public function testFindById()
    {
        $locationId = 1;
        $location = $this->prophesize(Location::class);
        $this->injector->getProphecy(LocationRepositoryInterface::class)
            ->findById($locationId)
            ->willReturn($location->reveal())
            ->shouldBeCalled();
        static::assertSame($location->reveal(), $this->locationService->findById($locationId));
    }

    public function testFindAll()
    {
        $location = $this->prophesize(Location::class);
        $this->injector->getProphecy(LocationRepositoryInterface::class)
            ->findAll()
            ->willReturn([$location->reveal()])
            ->shouldBeCalled();
        static::assertSame([$location->reveal()], $this->locationService->findAll());
    }

    public function testUpdateLocationCommand()
    {
        $locationId = 1;
        $location = $this->prophesize(Location::class);
        $command = new UpdateLocationCommand(
            $locationId,
            new Name('name'),
            new Description('description'),
            new Capacity(1)
        );
        $this->injector->getProphecy(LocationRepositoryInterface::class)
            ->findById($locationId)
            ->willReturn($location->reveal())
            ->shouldBeCalled();
        $this->injector->getProphecy(LocationRepositoryInterface::class)
            ->save($location->reveal())
            ->shouldBeCalled();
        static::assertEmpty($this->locationService->updateLocation($command));
    }

    public function testSave()
    {
        $location = $this->prophesize(Location::class);
        $locationId = 1;
        $this->injector->getProphecy(LocationRepositoryInterface::class)
            ->save($location->reveal())
            ->willReturn($locationId)
            ->shouldBeCalled();
        static::assertSame($locationId, $this->locationService->save($location->reveal()));
    }
}

