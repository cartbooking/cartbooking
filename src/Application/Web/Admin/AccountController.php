<?php

namespace CartBooking\Application\Web\Admin;

use CartBooking\Application\WebPublisherService;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints as Assert;
use Twig_Environment;

class AccountController
{
    /** @var WebPublisherService */
    private $publisherService;
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
        Request $request,
        Session $session,
        Twig_Environment $twig,
        WebPublisherService $publisherService
    ) {
        $this->formFactory = $formFactory;
        $this->request = $request;
        $this->publisherService = $publisherService;
        $this->twig = $twig;
        $this->session = $session;
    }

    public function indexAction(): Response
    {
        $user = $this->publisherService->getCurrentUser();
        $form = $this->formFactory->createBuilder(FormType::class, $user)
            ->add('email', EmailType::class, ['constraints' => [new Assert\NotBlank()]])
            ->add('full_name', TextType::class, ['constraints' => [new Assert\NotBlank()]])
            ->add('preferred_name', TextType::class, ['constraints' => [new Assert\NotBlank()]])
            ->add('email', TextType::class, [
                'constraints' => [new Assert\NotBlank(), new Assert\Email()]
            ])->add('phone', TextType::class, [
                'constraints' => [new Assert\NotBlank(), new Assert\Length(['min' => 6, 'max' => 11])]
            ])->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Passwords do not match',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                'constraints' => [new Assert\NotBlank(), new Assert\Length(['min' => 6, 'max' => 20])]
            ])->add('submit', SubmitType::class, [
                'label' => 'Save',
            ])->getForm();
        $form->handleRequest($this->request);
        if ($form->isValid()) {
            $data = $form->getData();
            $this->publisherService->updateUser($data);
            $this->session->getFlashBag()->add('info', 'Your account information was updated');
        }

        return (new Response())->setContent($this->twig->render('admin/account/index.twig', [
            'form' => $form->createView()
        ]));
    }
}
