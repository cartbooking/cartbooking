<?php

namespace Test\Unit\Infrastructure\Persistence\Doctrine;

use CartBooking\Infrastructure\Persistence\Doctrine\Repository\DoctrineLocationRepository;
use CartBooking\Model\Location\Location;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Test\AutoMockingTest;

class DoctrineLocationRepositoryTest extends AutoMockingTest
{
    /** @var DoctrineLocationRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->repository = $this->injector->create(DoctrineLocationRepository::class);
    }

    public function testFindById()
    {
        $id = 1;
        $location = $this->prophesize(Location::class);
        $this->injector->getProphecy(EntityManager::class)->find(Location::class, $id)
            ->willReturn($location->reveal())->shouldBeCalled();
        static::assertSame($location->reveal(), $this->repository->findById($id));
    }

    public function testFindAll()
    {
        $location = $this->prophesize(Location::class);
        $entityRepository = $this->prophesize(EntityRepository::class);
        $entityRepository->findAll()->willReturn([$location]);
        $this->injector->getProphecy(EntityManager::class)->getRepository(Location::class)
            ->willReturn($entityRepository->reveal())->shouldBeCalled();
        static::assertSame([$location->reveal()], $this->repository->findAll());
    }
}
