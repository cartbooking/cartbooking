<?php

namespace CartBooking\Application\Web;

use CartBooking\Application\WebPublisherService;
use CartBooking\Model\Booking\BookingId;
use CartBooking\Model\Booking\BookingRepository;
use CartBooking\Model\Location\LocationRepositoryInterface;
use CartBooking\Model\Shift\ShiftRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class PlacementsController
{
    /** @var Request */
    private $request;
    /** @var Response */
    private $response;
    /** @var Twig_Environment */
    private $twig;
    /** @var BookingRepository */
    private $bookingRepository;
    /** @var ShiftRepositoryInterface */
    private $shiftRepository;
    /** @var LocationRepositoryInterface */
    private $locationRepository;
    /** @var WebPublisherService */
    private $publisherService;

    public function __construct(
        BookingRepository $bookingRepository,
        LocationRepositoryInterface $locationRepository,
        WebPublisherService $publisherService,
        Request $request,
        Response $response,
        ShiftRepositoryInterface $shiftRepository,
        Twig_Environment $twig
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->locationRepository = $locationRepository;
        $this->request = $request;
        $this->publisherService = $publisherService;
        $this->response = $response;
        $this->twig = $twig;
        $this->shiftRepository = $shiftRepository;
    }

    /**
     * @return Response
     * @throws \UnexpectedValueException
     */
    public function indexAction(): Response
    {
        $userId = $this->publisherService->getCurrentPublisher()->getId();
        $bookings = [];
        foreach ($this->bookingRepository->findPendingBookingsForUser($userId, new \DateTimeImmutable()) as $booking) {
            $shift = $this->shiftRepository->findById($booking->getShiftId());
            $location = $this->locationRepository->findById($shift->getLocationId());

            if ($location !== null) {
                $bookings[] = [
                    'booking_id' => $booking->getId(),
                    'display_date' => $booking->getDate()->format('M jS'),
                    'location_name' => $location->getName(),
                    'display_time' => $shift->getStartTime()->format('g:ia')
                ];
            }
        }
        return $this->response->setContent($this->twig->render('placements/index.twig', ['bookings' => $bookings]));
    }

    /**
     * @param int $bookingId
     * @return Response
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     * @throws \UnexpectedValueException
     */
    public function reportAction(int $bookingId): Response
    {
        $booking = $this->bookingRepository->findById(new BookingId($bookingId));
        $shift = $this->shiftRepository->findById($booking->getShiftId());
        $location = $this->locationRepository->findById($shift->getLocationId());
        return $this->response->setContent($this->twig->render('placements/booking_report.twig', [
            'booking_id' => $booking->getId(),
            'display_date' => $booking->getDate()->format('M jS'),
            'location_name' => $location->getName(),
            'display_time' => $shift->getStartTime()->format('H:i:s'),
        ]));
    }

    public function postAction(): Response
    {
        $booking = $this->bookingRepository->findById(new BookingId($this->request->get('booking_id')));
        if ($booking !== null && $booking->isConfirmed() && !$booking->isRecorded()) {
            $booking->setPlacements((int)$this->request->get('placements'));
            $booking->setVideos((int)$this->request->get('videos'));
            $booking->setRequests((int)$this->request->get('requests'));
            $booking->setComments((string)$this->request->get('comments'));
            $booking->setRecorded(true);
            $this->bookingRepository->save($booking);
            return $this->response->setContent($this->twig->render('placements/submitted_successfully.twig'));
        }
        return $this->response->setContent($this->twig->render('placements/submit_failed.twig'), []);
    }
}
