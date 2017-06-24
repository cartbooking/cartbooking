<?php

namespace Test\Unit\Application\Http;

use CartBooking\Application\Web\PublishersController;
use Prophecy\Argument;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
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
        $formBuilder = $this->prophesize(FormBuilderInterface::class);
        $formBuilder->add(Argument::any(), Argument::any(), Argument::any())->willReturn($formBuilder);
        $formBuilder->getForm()->willReturn($this->prophesize(FormInterface::class)->reveal());
        $this->injector->getProphecy(FormFactory::class)
            ->createBuilder(FormType::class, Argument::any())
            ->willReturn($formBuilder->reveal());
        static::assertInstanceOf(Response::class, $this->controller->indexAction());
    }
}
