<?php

namespace Test\Application\Http;

use CartBooking\Application\Http\PublishersController;
use CartBooking\Booking\BookingRepository;
use CartBooking\Publisher\PioneerRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class PublishersControllerTest extends TestCase
{
    /** @var  PublishersController */
    private $controller;

    public function setUp()
    {
        parent::setUp();
        $request = $this->prophesize(Request::class)->reveal();
        $bookingRepository = $this->prophesize(BookingRepository::class)->reveal();
        $pioneerRepository = $this->prophesize(PioneerRepository::class)->reveal();
        $twig = $this->prophesize(Twig_Environment::class)->reveal();
        $this->controller = new PublishersController($request, $bookingRepository, $pioneerRepository, $twig);
    }

    public function testIndexAction()
    {
        static::assertInstanceOf(Response::class, $this->controller->indexAction());
    }
}
