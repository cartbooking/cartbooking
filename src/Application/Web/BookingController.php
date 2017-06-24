<?php

namespace CartBooking\Application\Web;

use CartBooking\Application\PublisherService;
use CartBooking\Infrastructure\Persistence\Doctrine\Repository\DoctrineLocationRepository;
use CartBooking\Model\Booking\Booking;
use CartBooking\Model\Booking\BookingRepository;
use CartBooking\Model\Booking\BookingService;
use CartBooking\Model\Booking\Command\AddPublishersCommand;
use CartBooking\Model\Booking\Command\CreateBookingCommand;
use CartBooking\Model\Booking\Command\DeletePublisherFromBookingCommand;
use CartBooking\Model\Booking\Exception\InvalidArgumentException;
use CartBooking\Model\Booking\Exception\InvalidMobilePhone;
use CartBooking\Model\Publisher\PublisherRepository;
use CartBooking\Model\Shift\Shift;
use CartBooking\Model\Shift\ShiftRepositoryInterface;
use DateInterval;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class BookingController
{
    /** @var Request */
    private $request;
    /** @var BookingRepository */
    private $bookingRepository;
    /** @var DoctrineLocationRepository */
    private $locationRepository;
    /** @var PublisherRepository */
    private $pioneerRepository;
    /** @var ShiftRepositoryInterface */
    private $shiftRepository;
    /** @var Twig_Environment */
    private $twig;
    /** @var BookingService */
    private $bookingService;
    /** @var PublisherService */
    private $publisherService;

    public function __construct(
        Request $request,
        BookingRepository $bookingRepository,
        BookingService $bookingService,
        DoctrineLocationRepository $locationRepository,
        PublisherRepository $pioneerRepository,
        PublisherService $publisherService,
        ShiftRepositoryInterface $shiftRepository,
        Twig_Environment $twig
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->bookingService = $bookingService;
        $this->locationRepository = $locationRepository;
        $this->pioneerRepository = $pioneerRepository;
        $this->publisherService = $publisherService;
        $this->request = $request;
        $this->shiftRepository = $shiftRepository;
        $this->twig = $twig;
    }

    public function indexAction(): Response
    {
        $userId = $this->publisherService->getCurrentPublisher()->getId();
        if ($this->request->get('m') !== null) {
            $month = $this->request->get('m');
            if ($month === 'n') {
                $month = date('F', strtotime('first day of next month'));
                $year = date('Y', strtotime('first day of next month'));
                if ($this->request->get('d') !== null) {
                    $highlighted = $this->request->get('d');
                } else {
                    $highlighted = 1;
                }
            } else {
                $month = date('F');
                $year = date('Y');
                if ($this->request->get('d') !== null) {
                    $highlighted = $this->request->get('d');
                } else {
                    $highlighted = date('j', strtotime('today'));
                }
            }
        } else {
            $month = date('F');
            $year = date('Y');
            if ($this->request->get('d') !== null) {
                $highlighted = $this->request->get('d');
            } else {
                $highlighted = date('j', strtotime('today'));
            }
        }
        $days = date('t', strtotime('1st ' . $month . ' ' . $year . ''));
        if ($month === date('F')) {
            $new_month = 'n';
        } else {
            $new_month = '';
        }
        $firstDayOfTheMonth = (int)date('w', strtotime('1st ' . $month . ' ' . $year . ''));
        if ($this->request->get('select_date') !== null) {
            $select_date = strtotime($this->request->get('select_date'));
        } else {
            $select_date = strtotime('' . $month . ' ' . $highlighted . ', ' . $year . '');
        }
        return (new Response())->setContent($this->twig->render('booking/index.twig', [
            'title' => "$month Calendar",
            'month' => $month,
            'new_month' => $new_month,
            'first_day_of_month' => $firstDayOfTheMonth,
            'days' => $days,
            'year' => $year,
            'highlighted' => $highlighted,
            'shifts' => $this->populateMyShifts($userId, DateTimeImmutable::createFromFormat('FY|', $month . $year)),
            'select_day' => new DateTimeImmutable($select_date ? "@$select_date": 'now'),
            'cancel_time' => (new DateTimeImmutable('now'))->add(new DateInterval('P1D')),
            'locations' => $this->populateLocations(new DateTimeImmutable($select_date ? "@$select_date": 'now')),
            'user_id' => $userId,
            'admin' => ['phone' => '0457406625']
        ]));
    }

    private function populateMyShifts(int $userId, DateTimeImmutable $dateTime): array
    {
        $shifts = [];
        $daysOfMonth = (int)$dateTime->format('t');
        for ($i = 0; $i < $daysOfMonth; ++$i) {
            $day = $dateTime->add(new DateInterval("P{$i}D"));
            $shifts[$i + 1] = [
                'has_booking' => false,
                'is_recorded' => false,
            ];
            foreach ($this->bookingRepository->findByPublisherIdAndDate($userId, $day) as $booking) {
                $shifts[$i + 1] = [
                    'has_booking' => true,
                    'is_recorded' => $booking->isRecorded() && $booking->isConfirmed(),
                ];
            }
        }
        return $shifts;
    }

    private function populateLocations(DateTimeImmutable $dateTime): array
    {
        $locations = [];
        foreach ($this->locationRepository->findAll() as $location) {
            $shifts = $this->shiftRepository->findByDayAndLocation($dateTime, $location->getId());
            $locations[] = [
                'id' => $location->getId(),
                'name' => $location->getName(),
                'capacity' => $location->getCapacity(),
                'shifts' => array_map(function (Shift $shift) use ($dateTime) {
                    $booking = $this->bookingRepository->findByShiftAndDate($shift->getId(), $dateTime);
                    $bookingData = [
                        'id' => null,
                        'confirmed' => false,
                        'recorded' => false,
                        'overseer' => ['id' => 0, 'gender' => '', 'name' => '', 'phone' => ''],
                        'pioneer' => ['id' => 0, 'gender' => '', 'name' => '', 'phone' => ''],
                        'pioneer_b' => ['id' => 0, 'gender' => '', 'name' => '', 'phone' => ''],
                        'amount_publishers' => 0,
                        'publishers' => []
                    ];
                    if ($booking instanceof Booking) {
                        $bookingData['publishers'] = $booking->getPublishers();
                        $bookingData['id'] = $booking->getId();
                        $bookingData['confirmed'] = $booking->isConfirmed();
                        $bookingData['recorded'] = $booking->isRecorded();
                    }
                    return [
                        'id' => $shift->getId(),
                        'start_time' => $shift->getStartTime(),
                        'booking' => $bookingData
                    ];
                }, iterator_to_array($shifts))
            ];
        }
        return $locations;
    }

    public function postAction(): Response
    {
        if (!empty($this->request->get('delete'))) {
            $bookingId = (int)$this->request->get('booking_id');
            $mobile = $this->request->get('delete');
            $publisher = $this->pioneerRepository->findByPhone($mobile);
            if ($publisher === null) {
                throw new InvalidArgumentException();
            }
            $this->bookingService->removePublishers(new DeletePublisherFromBookingCommand(
                $bookingId,
                $publisher->getId()
            ));
        } else {
            if (!empty($this->request->get('booking_id'))) {
                $bookingId = (int)$this->request->get('booking_id');
                $this->bookingService->addPublishers(new AddPublishersCommand($bookingId, array_merge(
                    [$this->request->get('user')],
                    $this->mapVolunteersPhoneToId($this->request->get('volunteers', []))
                )));

            } else {
                $bookingId = $this->bookingService->createBooking(new CreateBookingCommand(
                    (int)$this->request->get('shift'),
                    $this->request->get('date'),
                    array_merge(
                        [$this->request->get('user')],
                        $this->mapVolunteersPhoneToId($this->request->get('volunteers', []))
                    )
                ));
            }
        }
        $booking = $this->bookingService->findById($bookingId);

        try {
            return (new Response())->setContent($this->twig->render('booking/result.twig', [
                'title' => 'Booking Accepted',
                'display' => 'Thank You',
                'description' => 'Your booking has been entered',
                'select_date' => $this->request->get('date'),
                'booking' => $booking,
                'result' => true,
            ]));
        } catch (InvalidArgumentException $e) {
            return (new Response())->setContent($this->twig->render('booking/result.twig', [
                'title' => 'Fail Results',
                'select_date' => $this->request->get('date')
            ]));
        }
    }

    private function mapVolunteersPhoneToId(array $phones): array
    {
        $phones = array_filter(
            $phones,
            function ($id) {
                return $id !== '' && $id !== $this->request->get('delete');
            }
        );
        $ids = [];
        foreach ($phones as $phone) {
            $publisher = $this->pioneerRepository->findByPhone($phone);
            if ($publisher === null) {
                throw new InvalidMobilePhone();
            }
            $ids[] = $publisher->getId();
        }
        return $ids;
    }
}
