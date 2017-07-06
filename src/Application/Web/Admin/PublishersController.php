<?php

namespace CartBooking\Application\Web\Admin;

use CartBooking\Model\Booking\BookingRepository;
use CartBooking\Model\Publisher\Command\AddPublisherCommand;
use CartBooking\Model\Publisher\Command\UpdatePasswordCommand;
use CartBooking\Model\Publisher\Command\UpdatePublisherCommand;
use CartBooking\Model\Publisher\PublisherRepository;
use CartBooking\Model\Publisher\PublisherService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints as Assert;
use Twig_Environment;

class PublishersController
{
    /** @var Request */
    private $request;
    /** @var PublisherRepository */
    private $pioneerRepository;
    /** @var Twig_Environment */
    private $twig;
    /** @var BookingRepository */
    private $bookingRepository;
    /** @var FormFactory */
    private $formFactory;
    /** @var Session */
    private $session;
    /** @var PublisherService */
    private $publisherService;

    public function __construct(
        Request $request,
        Session $session,
        BookingRepository $bookingRepository,
        PublisherRepository $pioneerRepository,
        Twig_Environment $twig,
        FormFactory $formFactory,
        PublisherService $publisherService
    ) {
        $this->request = $request;
        $this->pioneerRepository = $pioneerRepository;
        $this->twig = $twig;
        $this->bookingRepository = $bookingRepository;
        $this->formFactory = $formFactory;
        $this->session = $session;
        $this->publisherService = $publisherService;
    }

    public function indexAction(): Response
    {
        $form = $this->createPublisherForm([]);
        $form->handleRequest($this->request);
        if ($form->isValid()) {
            $data = $form->getData();
            $publisherId = $this->publisherService->addPublisher(new AddPublisherCommand(
                $data['full_name'],
                $data['full_name'],
                $data['email'],
                $data['phone'],
                $data['gender']
            ));
            $this->publisherService->updatePublisherPassword(new UpdatePasswordCommand($publisherId, 'password1'));
            $this->session->getFlashBag()->add('info', 'Publisher has been added');
        }

        return (new Response())->setContent($this->twig->render('admin/publishers/index.twig', [
            'form' => $form->createView()
        ]));
    }

    public function editAction($publisherId): Response
    {
        $publisherData = $this->publisherService->getPublisherData($publisherId);
        if ($publisherData === []) {
            return new RedirectResponse('/publishers');
        }
        $form = $this->createPublisherForm($publisherData);
        $form->handleRequest($this->request);
        if ($form->isValid()) {
            $data = $form->getData();
            $this->publisherService->updatePublisher(new UpdatePublisherCommand(
                $publisherId,
                $data['full_name'],
                $data['phone'],
                $data['email']
            ));
            $this->session->getFlashBag()->add('info', 'User has been updated');
            $this->session->getFlashBag()->get('info');
        }
        return new Response($this->twig->render('admin/publishers/index.twig', [
            'form' => $form->createView()
        ]));
    }

    public function searchAction($name): Response
    {
        return (new Response())->setContent($this->twig->render('admin/publishers/search.twig', [
            'publishers' => $this->pioneerRepository->findByName($name),
        ]));
    }

    public function lowParticipants(): Response
    {
        $publishersBookings = [];
        foreach ($this->pioneerRepository->findAll() as $publisher) {
            $publishersBookings[$publisher->getId()] = [
                'count' => count($this->bookingRepository->findByPublisherId($publisher->getId())),
                'name' => $publisher->getFullName(),
            ];
        }
        return (new Response())->setContent($this->twig->render('admin/publishers/low_participants.twig', [
            'participants' => count(array_filter($publishersBookings, function (array $data) {
                return $data['count'] < 5;
            }))
        ]));
    }

    public function participation(): Response
    {
        return new Response();
    }

    /**
     * @param $data
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createPublisherForm($data): \Symfony\Component\Form\FormInterface
    {
        $form = $this->formFactory->createBuilder(FormType::class, $data)
            ->add('email', EmailType::class, ['constraints' => [new Assert\NotBlank()]])
            ->add('full_name', TextType::class, ['constraints' => [new Assert\NotBlank()]])
            ->add('preferred_name', TextType::class, ['constraints' => [new Assert\NotBlank()]])
            ->add('email', TextType::class, [
                'constraints' => [new Assert\NotBlank(), new Assert\Email()]
            ])->add('phone', TextType::class, [
                'constraints' => [new Assert\NotBlank(), new Assert\Length(['min' => 6, 'max' => 11])]
            ])->add('gender', ChoiceType::class, [
                'choices' => ['male' => 'm', 'female' => 'f'],
                'expanded' => false,
            ])->add('submit', SubmitType::class, [
                'label' => 'Save',
            ])->getForm();
        return $form;
    }
}
