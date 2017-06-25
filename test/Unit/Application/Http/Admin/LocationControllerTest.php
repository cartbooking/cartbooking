<?php

namespace Test\Unit\Application\Http\Admin;

use CartBooking\Application\Web\Admin\LocationController;
use CartBooking\Model\Location\Location;
use CartBooking\Model\Location\LocationService;
use Prophecy\Argument;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Test\AutoMockingTest;

class LocationControllerTest extends AutoMockingTest
{
    /** @var LocationController */
    private $controller;

    public function setUp()
    {
        parent::setUp();
        $this->controller = $this->injector->create(LocationController::class);
    }

    public function testIndexAction()
    {
        $this->injector->getProphecy(LocationService::class)->findAll()->shouldBeCalled();
        static::assertInstanceOf(Response::class, $this->controller->indexAction());
    }

    public function testEditActionBeforeSubmit()
    {
        $locationId = 1;
        $location = $this->prophesize(Location::class);
        $form = $this->prophesize(FormInterface::class);
        $formBuilder = $this->prophesize(FormBuilderInterface::class);
        $formBuilder->add(Argument::any(), Argument::any(), Argument::any())->willReturn($formBuilder);
        $formBuilder->getForm()->willReturn($form->reveal());
        $this->injector->getProphecy(LocationService::class)->findById($locationId)->willReturn($location->reveal());
        $this->injector->getProphecy(FormFactory::class)->createBuilder(FormType::class, $location->reveal())
            ->willReturn($formBuilder);
        static::assertInstanceOf(Response::class, $this->controller->editAction($locationId));
    }

    public function testEditActionPostSubmit()
    {
        $locationId = 1;
        $location = $this->prophesize(Location::class);
        $form = $this->prophesize(FormInterface::class);
        $form->handleRequest($this->injector->getProphecy(Request::class))->shouldBeCalled();
        $form->isValid()->willReturn(true);
        $form->getData()->willReturn($location->reveal());
        $form->createView()->shouldBeCalled();
        $formBuilder = $this->prophesize(FormBuilderInterface::class);
        $formBuilder->add(Argument::any(), Argument::any(), Argument::any())->willReturn($formBuilder);
        $formBuilder->getForm()->willReturn($form->reveal());
        $this->injector->getProphecy(LocationService::class)->findById($locationId)->willReturn($location->reveal());
        $this->injector->getProphecy(LocationService::class)->save($location->reveal())->shouldBeCalled();
        $this->injector->getProphecy(FormFactory::class)->createBuilder(FormType::class, $location->reveal())
            ->willReturn($formBuilder);
        static::assertInstanceOf(Response::class, $this->controller->editAction($locationId));
    }
}
