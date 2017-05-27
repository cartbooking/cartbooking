<?php

namespace CartBooking\Application\Http;

use CartBooking\Booking\BookingRepository;
use CartBooking\Booking\BookingService;
use CartBooking\Booking\Command\CreateBookingCommand;
use CartBooking\Booking\Exception\InvalidMobilePhone;
use CartBooking\Location\LocationRepository;
use CartBooking\Publisher\PublisherRepository;
use CartBooking\Shift\Shift;
use CartBooking\Shift\ShiftRepository;
use DateInterval;
use DateTime;
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
    /** @var LocationRepository */
    private $locationRepository;
    /** @var PublisherRepository */
    private $pioneerRepository;
    /** @var ShiftRepository */
    private $shiftRepository;
    /** @var Twig_Environment */
    private $twig;
    /** @var BookingService */
    private $bookingService;

    public function __construct(
        Request $request,
        BookingRepository $bookingRepository,
        BookingService $bookingService,
        LocationRepository $locationRepository,
        PublisherRepository $pioneerRepository,
        ShiftRepository $shiftRepository,
        Twig_Environment $twig
    ) {
        $this->request = $request;
        $this->bookingRepository = $bookingRepository;
        $this->locationRepository = $locationRepository;
        $this->pioneerRepository = $pioneerRepository;
        $this->shiftRepository = $shiftRepository;
        $this->twig = $twig;
        $this->bookingService = $bookingService;
    }

    public function indexAction(int $userId): Response
    {

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
            'shifts' => $this->populateShifts($userId, DateTimeImmutable::createFromFormat('FY|', $month . $year)),
            'select_day' => new DateTimeImmutable("@$select_date"),
            'cancel_time' => (new DateTimeImmutable('now'))->add(new DateInterval('P1D')),
            'locations' => $this->populateLocations(new DateTimeImmutable("@$select_date")),
            'user_id' => $userId
        ]));
    }

    private function populateShifts(int $userId, DateTimeImmutable $dateTime): array
    {
        $shifts = [];
        for ($i = 0; $i < (int)$dateTime->format('t'); ++$i) {
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
            $shifts = $this->shiftRepository->findByDayAndLocation((int)$dateTime->format('w'), $location->getId());
            $locations[] = [
                'id' => $location->getId(),
                'name' => $location->getName(),
                'capacity' => $location->getCapacity(),
                'shifts' => array_map(function (Shift $shift) use ($dateTime) {
                    $booking = $this->bookingRepository->findByShiftAndDate($shift->getId(), $dateTime);
                    $bookingData = [
                        'confirmed' => false,
                        'recorded' => false,
                        'overseer' => ['id' => 0, 'gender' => '', 'name' => '', 'phone' => ''],
                        'pioneer' => ['id' => 0, 'gender' => '', 'name' => '', 'phone' => ''],
                        'pioneer_b' => ['id' => 0, 'gender' => '', 'name' => '', 'phone' => ''],
                        'amount_publishers' => 0,
                    ];
                    if ($booking !== null) {
                        $bookingData['confirmed'] = $booking->isConfirmed();
                        $bookingData['recorded'] = $booking->isRecorded();
                        $overseer = $this->pioneerRepository->findById($booking->getOverseerId());
                        if ($overseer) {
                            $bookingData['overseer'] = [
                                'id' => $overseer->getId(),
                                'gender' => $overseer->getGender(),
                                'name' => $overseer->getFirstName() . ' ' . $overseer->getLastName(),
                                'phone' => $overseer->getPhone(),
                            ];
                            $bookingData['amount_publishers']++;
                        }
                        $pioneer = $this->pioneerRepository->findById($booking->getPioneerId());
                        if ($pioneer) {
                            $bookingData['pioneer'] = [
                                'id' => $pioneer->getId(),
                                'gender' => $pioneer->getGender(),
                                'name' => $pioneer->getFirstName() . ' ' . $pioneer->getLastName(),
                                'phone' => $pioneer->getPhone(),
                            ];
                            $bookingData['amount_publishers']++;
                        }
                        $pioneerB = $this->pioneerRepository->findById($booking->getPioneerBId());
                        if ($pioneerB) {
                            $bookingData['pioneer_b'] = [
                                'id' => $pioneerB->getId(),
                                'gender' => $pioneerB->getGender(),
                                'name' => $pioneerB->getFirstName() . ' ' . $pioneerB->getLastName(),
                                'phone' => $pioneerB->getPhone(),
                            ];
                            $bookingData['amount_publishers']++;
                        }
                    }
                    return [
                        'id' => $shift->getId(),
                        'start_time' => DateTime::createFromFormat('H:i:s|', $shift->getStartTime()),
                        'booking' => $bookingData
                    ];
                }, $shifts)
            ];
        }
        return $locations;
    }

    public function postAction(): Response
    {
        $booking = $this->bookingService->createBooking(new CreateBookingCommand(
            (int)$this->request->get('shift'),
            $this->request->get('date'),
            array_merge(
                [$this->request->get('user')],
                $this->mapVolunteersPhoneToId($this->request->get('volunteers', []))
            )
        ));

        return (new Response())->setContent($this->twig->render('booking/result.twig', ['booking' => $booking]));
    }

    private function mapVolunteersPhoneToId(array $phones): array
    {
        $phones = array_filter(
            $phones,
            function ($id) {
                return $id !== '';
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
