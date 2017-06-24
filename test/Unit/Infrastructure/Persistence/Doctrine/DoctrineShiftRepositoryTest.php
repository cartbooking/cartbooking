<?php

namespace Test\Unit\Infrastructure\Persistence\Doctrine;

use CartBooking\Infrastructure\Persistence\Doctrine\Repository\DoctrineShiftRepository;
use CartBooking\Model\Shift\Shift;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Prophecy\Argument;
use Test\AutoMockingTest;

class DoctrineShiftRepositoryTest extends AutoMockingTest
{
    /** @var DoctrineShiftRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->repository = $this->injector->create(DoctrineShiftRepository::class);
    }

    public function testFindById()
    {
        $shiftId = 1;
        $shift = $this->prophesize(Shift::class);
        $this->injector->getProphecy(EntityManager::class)->find(Shift::class, $shiftId)
            ->willReturn($shift->reveal())
            ->shouldBeCalled();
        static::assertSame($shift->reveal(), $this->repository->findById($shiftId));
    }

    public function testFindByCriteria()
    {
        $repository = $this->prophesize(EntityRepository::class);
        $repository->matching(Argument::any())->willReturn(new ArrayCollection([$this->prophesize(Shift::class)]))
            ->shouldBeCalled();
        $this->injector->getProphecy(EntityManager::class)->getRepository(Shift::class)
            ->willReturn($repository->reveal())
            ->shouldBeCalled();
        $this->repository->findByDayAndLocation(new DateTimeImmutable(), 1);
    }
}
