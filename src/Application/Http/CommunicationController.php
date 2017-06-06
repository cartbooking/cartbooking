<?php

namespace CartBooking\Application\Http;

use CartBooking\Application\EmailService;
use CartBooking\Booking\BookingRepository;
use CartBooking\Infrastructure\Persistence\Doctrine\Repository\DoctrineLocationRepository;
use CartBooking\Publisher\PublisherRepository;
use CartBooking\Shift\ShiftRepository;
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
    /** @var ShiftRepository */
    private $shiftRepository;
    /** @var DoctrineLocationRepository */
    private $locationRepository;

    public function __construct(
        EmailService $emailService,
        Request $request,
        Response $response,
        Twig_Environment $twig,
        BookingRepository $bookingRepository,
        DoctrineLocationRepository $locationRepository,
        PublisherRepository $pioneerRepository,
        ShiftRepository $shiftRepository
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
        return $this->response->setContent($this->twig->render('communication.twig'));
    }

    public function sendBookingReminderEmailsAction(): Response
    {
        $subject = 'Reminder to record placements';
        $dateTo = (new DateTimeImmutable())->sub(new DateInterval('P1D'));
        $sent = 0;
        $data = [];
        foreach ($this->bookingRepository->findNonRecordedBookingsOlderThan($dateTo) as $booking) {
            $overseer = $this->pioneerRepository->findById($booking->getOverseerId());
            $shift = $this->shiftRepository->findById($booking->getShiftId());
            $location = $this->locationRepository->findById($shift->getLocationId());
            $data[$overseer->getId()]['overseer'] = $overseer;
            $data[$overseer->getId()]['first_name'] = $overseer->getFirstName();
            $data[$overseer->getId()]['bookings'][] = [
                'location_name' => $location->getName(),
                'display_date' => $booking->getDate()->format('F jS'),
                'display_time' => date('g:ia', strtotime($shift->getStartTime())),
            ];
        }
        foreach ($data as $datum) {
            $this->emailService->sendEmailTo(
                $datum['overseer'],
                $subject,
                $this->twig->render('emails/placement_reminder.twig', $datum)
            );
        }
        return $this->response->setContent($this->twig->render('communication/placements_reminders.twig', [
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
                'display_time' => $shift->getStartTime()
            ];
        }
        $counter = 0;
        if ($context !== []) {
            foreach ($this->pioneerRepository->findByGender('m') as $pioneer) {
                $context['first_name'] = $pioneer->getFirstName();
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
        foreach ($this->bookingRepository->findBookingsNeedingVolunteersBetween($from, $to) as $booking) {
            $shift = $this->shiftRepository->findById($booking->getShiftId());
            $location = $this->locationRepository->findById($shift->getLocationId());
            $context['context'][$booking->getId()] = [
                'display_date' => $booking->getDate()->format('D, F jS'),
                'location_name' => $location->getName(),
                'display_time' => $shift->getStartTime(),
                'has_overseer' => $booking->getOverseerId() > 0,
                'pioneer_gender' => $booking->getPioneerId() > 0 ? $this->pioneerRepository->findById($booking->getPioneerId())->getGender() : null,
                'second_pioneer_gender' => $booking->getPioneerBId() > 0 ? $this->pioneerRepository->findById($booking->getPioneerBId())->getGender() : null,
            ];
        }
        $counter = 0;
        if ($context !== []) {
            foreach ($this->pioneerRepository->findActive() as $pioneer) {
                $context['first_name'] = $pioneer->getFirstName();
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
                'communication/emails_sent.twig',
                [
                    'title' => 'Volunteers request emails',
                    'amount_email_sent' => $counter,
                    'message' => 'Volunteer needed requested'
                ]
            )
        );
    }
}
