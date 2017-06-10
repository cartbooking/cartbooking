<?php

namespace CartBooking\Application\Web;

use CartBooking\Booking\BookingRepository;
use CartBooking\Publisher\PublisherRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class ExperiencesController
{
    /** @var Request */
    private $request;
    /** @var Response */
    private $response;
    /** @var Twig_Environment */
    private $twig;
    /** @var BookingRepository */
    private $bookingRepository;
    /** @var PublisherRepository */
    private $pioneerRepository;

    public function __construct(Request $request, Response $response, Twig_Environment $twig, BookingRepository $bookingRepository, PublisherRepository $pioneerRepository)
    {
        $this->request = $request;
        $this->response = $response;
        $this->twig = $twig;
        $this->bookingRepository = $bookingRepository;
        $this->pioneerRepository = $pioneerRepository;
    }

    public function indexAction()
    {
        $viewData = [];
        foreach ($this->bookingRepository->findUnseenBookingsComments() as $booking) {
            $overseer = $this->pioneerRepository->findById($booking->getOverseerId());
            $viewData[] = [
                'booking_id' => $booking->getId(),
                'booking_comments' => $booking->getComments(),
                'date' => $booking->getDate(),
                'overseer_first_name' => $overseer->getFirstName(),
                'overseer_last_name' => $overseer->getLastName(),
            ];
        }
        return $this->response->setContent($this->twig->render('experiences.twig', ['view_data' => $viewData]));
    }

    public function postAction(int $bookingId)
    {
        $booking = $this->bookingRepository->findById($bookingId);
        if ($booking !== null) {
            $booking->setExperience(false);
        }
        $this->bookingRepository->save($booking);
        return new RedirectResponse('/experiences');
    }
}
