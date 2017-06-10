<?php

namespace Test\Unit\Publisher;

use CartBooking\Publisher\Publisher;
use Test\AutoMockingTest;

class PublisherTest extends AutoMockingTest
{
    public function testIsRelativeWith()
    {
        $publisherA = new Publisher(123);
        $publisherB = new Publisher(455);
        $publisherC = new Publisher(1);
        $publisherA->addRelatives([$publisherB]);
        static::assertTrue($publisherA->isRelativeTo($publisherB));
        static::assertFalse($publisherA->isRelativeTo($publisherC));
        static::assertFalse($publisherB->isRelativeTo($publisherA));
    }
}
