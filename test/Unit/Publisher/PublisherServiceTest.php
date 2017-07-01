<?php

namespace Test\Unit\Publisher;

use CartBooking\Model\Publisher\Command\AddPublisherCommand;
use CartBooking\Model\Publisher\Command\UpdatePasswordCommand;
use CartBooking\Model\Publisher\Command\UpdatePublisherCommand;
use CartBooking\Model\Publisher\Publisher;
use CartBooking\Model\Publisher\PublisherRepository;
use CartBooking\Model\Publisher\PublisherService;
use Prophecy\Argument;
use Test\AutoMockingTest;

class PublisherServiceTest extends AutoMockingTest
{
    /** @var PublisherService */
    private $publisherService;

    public function setUp()
    {
        parent::setUp();
        $this->publisherService = $this->injector->create(PublisherService::class);
    }

    public function testUpdatePublisher()
    {
        $publisherId = 1;
        $fullName = 'full name';
        $phone = '0404404';
        $email = 'email@test.com';
        $publisher = $this->prophesize(Publisher::class);
        $this->injector->getProphecy(PublisherRepository::class)->findById($publisherId)->willReturn($publisher->reveal());
        $this->injector->getProphecy(PublisherRepository::class)->save($publisher->reveal())->shouldBeCalled();
        $command = new UpdatePublisherCommand($publisherId, $fullName, $phone, $email);
        static::assertEmpty($this->publisherService->updatePublisher($command));
    }

    public function testUpdatePublisherPassword()
    {
        $publisherId = 1;
        $password = 'password';
        $publisher = $this->prophesize(Publisher::class);
        $this->injector->getProphecy(PublisherRepository::class)->findById($publisherId)->willReturn($publisher->reveal());
        $this->injector->getProphecy(PublisherRepository::class)->save($publisher->reveal())->shouldBeCalled();
        static::assertEmpty($this->publisherService->updatePublisherPassword(new UpdatePasswordCommand($publisherId, $password)));
    }

    public function testAddPublisher()
    {
        $fullName = 'full name';
        $phone = '0404404';
        $email = 'email@test.com';
        $gender = 'm';
        $this->injector->getProphecy(PublisherRepository::class)->save(Argument::type(Publisher::class))->shouldBeCalled();
        static::assertEmpty($this->publisherService->addPublisher(new AddPublisherCommand($fullName, $fullName, $email, $phone, $gender)));
    }
}
