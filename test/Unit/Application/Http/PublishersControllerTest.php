<?php

namespace Test\Unit\Application\Http;

use CartBooking\Application\Http\PublishersController;
use Symfony\Component\HttpFoundation\Response;
use Test\AutoMockingTest;

class PublishersControllerTest extends AutoMockingTest
{
    /** @var  PublishersController */
    private $controller;

    public function setUp()
    {
        parent::setUp();
        $this->controller = $this->injector->create(PublishersController::class);
    }

    public function testIndexAction()
    {
        static::assertInstanceOf(Response::class, $this->controller->indexAction());
    }
}
