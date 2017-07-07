<?php

namespace CartBooking\Application\Web\Admin;

use CartBooking\Model\Location\Location;
use CartBooking\Model\Location\LocationService;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints as Assert;
use Twig_Environment;

class LocationController
{
    /** @var LocationService */
    private $locationService;
    /** @var Twig_Environment */
    private $twig;
    /** @var FormFactory */
    private $formFactory;
    /** @var Request */
    private $request;
    /** @var Session */
    private $session;

    public function __construct(
        FormFactory $formFactory,
        LocationService $locationService,
        Request $request,
        Session $session,
        Twig_Environment $twig
    ) {
        $this->formFactory = $formFactory;
        $this->locationService = $locationService;
        $this->twig = $twig;
        $this->request = $request;
        $this->session = $session;
    }

    public function indexAction(): Response
    {
        $locations = $this->locationService->findAll();
        return new Response($this->twig->render('admin/locations/index.twig', ['locations' => $locations]));
    }

    public function editAction($locationId): Response
    {
        $location = $this->locationService->findById($locationId);
        $form = $this->formFactory->createBuilder(FormType::class, $location)
            ->add('name', TextType::class, ['constraints' => [new Assert\NotBlank()]])
            ->add('capacity', NumberType::class, ['constraints' => [new Assert\NotBlank()]])
            ->add('description', TextareaType::class, ['constraints' => [new Assert\NotBlank()], 'attr' => ['rows' => 10]])
            ->add('submit', SubmitType::class)
            ->getForm();
        $form->handleRequest($this->request);
        if ($form->isValid()) {
            $data = $form->getData();
            if ($data instanceof Location) {
                $this->locationService->save($location);
                $this->session->getFlashBag()->add('info', 'Location has been updated');
            }
        }
        return new Response($this->twig->render('admin/locations/location.twig', [
            'form' => $form->createView()
        ]));
    }
}
