<?php

namespace CartBooking\Application\Web\Admin;

use CartBooking\Application\EmailService;
use CartBooking\Model\Booking\BookingRepository;
use CartBooking\Model\Location\LocationRepositoryInterface;
use CartBooking\Model\Publisher\PublisherRepository;
use CartBooking\Model\Shift\ShiftRepositoryInterface;
use DateInterval;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class CommunicationController
{
    /** @var EmailService */
    private $emailService;
    /** @var Request */
    private $request;
    /** @var Response */
    private $response;
    /** @var Twig_Environment */
    private $twig;
    /** @var PublisherRepository */
    private $pioneerRepository;
    /** @var BookingRepository */
    private $bookingRepository;
    /** @var ShiftRepositoryInterface */
    private $shiftRepository;
    /** @var LocationRepositoryInterface */
    private $locationRepository;

    public function __construct(
        EmailService $emailService,
        Request $request,
        Response $response,
        Twig_Environment $twig,
        BookingRepository $bookingRepository,
        LocationRepositoryInterface $locationRepository,
        PublisherRepository $pioneerRepository,
        ShiftRepositoryInterface $shiftRepository
    ) {
        $this->emailService = $emailService;
        $this->request = $request;
        $this->response = $response;
        $this->twig = $twig;
        $this->pioneerRepository = $pioneerRepository;
        $this->bookingRepository = $bookingRepository;
        $this->shiftRepository = $shiftRepository;
        $this->locationRepository = $locationRepository;
    }

    public function indexAction(): Response
    {
        return $this->response->setContent($this->twig->render('admin/communication/index.twig'));
    }

    public function sendBookingReminderEmailsAction(): Response
    {
        $subject = 'Reminder to record placements';
        $dateTo = (new DateTimeImmutable())->sub(new DateInterval('P1D'));
        $sent = 0;
        foreach ($this->bookingRepository->findNonRecordedBookingsOlderThan($dateTo) as $booking) {
            $shift = $this->shiftRepository->findById($booking->getShiftId());
            $location = $this->locationRepository->findById($shift->getLocationId());
            foreach ($booking->getPublishers() as $publisher) {
                $this->emailService->sendEmailTo(
                    $publisher,
                    $subject,
                    $this->twig->render('emails/placement_reminder.twig', [
                        'first_name' => $publisher->getPreferredName(),
                        'location_name' => $location->getName(),
                        'display_date' => $booking->getDate()->format('F jS'),
                        'display_time' => $shift->getStartTime()->format('g:ia'),
                    ])
                );
                $sent++;
            }
        }
        return $this->response->setContent($this->twig->render('admin/communication/placements_reminders.twig', [
            'sent' => $sent,
        ]));
    }

    public function sendOverseerNeededEmailsAction(): Response
    {
        $from = new DateTimeImmutable();
        $to = (new DateTimeImmutable())->add(new DateInterval('P14D'));
        $context = [];
        foreach ($this->bookingRepository->findBookingsNeedingOverseerBetween($from, $to) as $booking) {
            $shift = $this->shiftRepository->findById($booking->getShiftId());
            $location = $this->locationRepository->findById($shift->getLocationId());
            $context[$booking->getId()] = [
                'display_date' => $booking->getDate()->format('D, F jS'),
                'location_name' => $location->getName(),
                'display_time' => $shift->getStartTime()->format('H:i:s')
            ];
        }
        $counter = 0;
        if ($context !== []) {
            foreach ($this->pioneerRepository->findByGender('m') as $pioneer) {
                $context['first_name'] = $pioneer->getPreferredName();
                $this->emailService->sendEmailTo(
                    $pioneer,
                    'Shift overseers needed',
                    $this->twig->render('emails/overseer_needed.twig', $context)
                );
                $counter++;
            }
        }
        return $this->response->setContent(
            $this->twig->render(
                'communication/emails_sent.twig',
                [
                    'title' => 'Overseers request emails',
                    'amount_email_sent' => $counter,
                    'message' => 'Overseers needed requested'
                ]
            )
        );
    }

    public function sendVolunteerNeededEmailsAction(): Response
    {
        $from = new DateTimeImmutable();
        $to = (new DateTimeImmutable())->add(new DateInterval('P14D'));
        $context = [];
        foreach ($this->bookingRepository->findNonConfirmedBookingsBetween($from, $to) as $booking) {
            $shift = $this->shiftRepository->findById($booking->getShiftId());
            $location = $this->locationRepository->findById($shift->getLocationId());
            $context['context'][(string)$booking->getId()] = [
                'display_date' => $booking->getDate()->format('D, F jS'),
                'location_name' => $location->getName(),
                'display_time' => $shift->getStartTime()->format('H:i:s'),
            ];
        }
        $counter = 0;
        if ($context !== []) {
            foreach ($this->pioneerRepository->findActive() as $pioneer) {
                $context['first_name'] = $pioneer->getPreferredName();
                $this->emailService->sendEmailTo(
                    $pioneer,
                    'Shift volunteers needed',
                    $this->twig->render('emails/volunteer_needed.twig', $context)
                );
                $counter++;
            }
        }
        return $this->response->setContent(
            $this->twig->render(
                'admin/communication/emails_sent.twig',
                [
                    'title' => 'Volunteers request emails',
                    'amount_email_sent' => $counter,
                    'message' => 'Volunteer needed requested'
                ]
            )
        );
    }
}
