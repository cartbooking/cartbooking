<?php

namespace CartBooking\Application\Http;

use CartBooking\Booking\BookingRepository;
use CartBooking\Publisher\PublisherRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    public function __construct(Request $request, BookingRepository $bookingRepository, PublisherRepository $pioneerRepository, Twig_Environment $twig)
    {
        $this->request = $request;
        $this->pioneerRepository = $pioneerRepository;
        $this->twig = $twig;
        $this->bookingRepository = $bookingRepository;
    }

    public function indexAction(): Response
    {
        return (new Response())->setContent($this->twig->render('publishers/index.twig'));
    }

    public function searchAction($name): Response
    {
        return (new Response())->setContent($this->twig->render('publishers/search.twig', [
            'publishers' => $this->pioneerRepository->findByName($name),
        ]));
    }

    public function lowParticipants(): Response
    {
        $publishersBookings = [];
        foreach ($this->pioneerRepository->findAll() as $publisher) {
            $publishersBookings[$publisher->getId()] = [
                'count' => count($this->bookingRepository->findByPublisherId($publisher->getId())),
                'name' => "{$publisher->getFirstName()} {$publisher->getLastName()}",
            ];
        }
        return (new Response())->setContent($this->twig->render('publishers/low_participants.twig', [
            'participants' => count(array_filter($publishersBookings, function (array $data) {
                return $data['count'] < 5;
            }))
        ]));
    }

    public function participation(): Response
    {
        return (new Response());
    }
}
