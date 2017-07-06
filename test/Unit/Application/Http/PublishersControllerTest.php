<?php

namespace Test\Unit\Application\Http;

use CartBooking\Application\Web\Admin\PublishersController;
use CartBooking\Model\Publisher\PublisherService;
use Prophecy\Argument;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
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

    public function testIndexActionOnSubmit()
    {
        $formBuilder = $this->prophesize(FormBuilderInterface::class);
        $formBuilder->add(Argument::any(), Argument::any(), Argument::any())->willReturn($formBuilder);
        $form = $this->prophesize(FormInterface::class);
        $form->handleRequest(Argument::any())->shouldBeCalled();
        $form->isValid()->willReturn(true);
        $form->getData()->willReturn([
            'full_name' => 'Full Name',
            'email' => 'Email@email.com',
            'phone' => '04040440',
            'gender' => 'm'
        ]);
        $form->createView()->shouldBeCalled();
        $formBuilder->getForm()->willReturn($form->reveal());
        $this->injector->getProphecy(FormFactory::class)
            ->createBuilder(FormType::class, Argument::any())
            ->willReturn($formBuilder->reveal());
        $publisherId = 1;
        $this->injector->getProphecy(PublisherService::class)
            ->addPublisher(Argument::any())
            ->willReturn($publisherId);
        $this->injector->getProphecy(PublisherService::class)
            ->updatePublisherPassword(Argument::any())
            ->shouldBeCalled();
        $this->injector->getProphecy(Session::class)->getFlashBag()
            ->willReturn($this->prophesize(FlashBagInterface::class)->reveal());
        static::assertInstanceOf(Response::class, $this->controller->indexAction());
    }
}
