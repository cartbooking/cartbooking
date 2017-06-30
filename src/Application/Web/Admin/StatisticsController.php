<?php

namespace CartBooking\Application\Web\Admin;

use CartBooking\Model\Booking\BookingRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class StatisticsController
{
    /** @var Request */
    private $request;
    /** @var Response */
    private $response;
    /** @var BookingRepository */
    private $bookingRepository;
    /** @var Twig_Environment */
    private $twig;

    public function __construct(Request $request, Response $response, BookingRepository $bookingRepository, Twig_Environment $twig)
    {
        $this->request = $request;
        $this->response = $response;
        $this->bookingRepository = $bookingRepository;
        $this->twig = $twig;
    }

    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        $thisMonth = date('n');
        $thisYear = date('Y');
        if ($thisMonth < 3) {
            $month = $thisMonth + 10;
            $year = $thisYear - 1;
        } else {
            $month = $thisMonth - 2;
            $year = $thisYear;
        }
        $month = date('Y-m-d', strtotime('1-'.$month.'-'.$year.''));
        $bookings = 0;
        $unconfirmed = 0;
        $monthPlacements = 0;
        $monthVideos = 0;
        $monthRequests = 0;
        $statistics = [];
        while (strtotime($month) <= strtotime('first day of next month')) {
            $nextMonth = date('Y-m-d', strtotime(''.$month.' + 1 month'));
            foreach ($this->bookingRepository->findByDateBetween(new DateTimeImmutable($month), new DateTimeImmutable($nextMonth)) as $booking){
                $bookings++;
                if ($booking->isConfirmed()) {
                    $unconfirmed++;
                }
                $monthPlacements += $booking->getPlacements();
                $monthVideos += $booking->getVideos();
                $monthRequests += $booking->getRequests();
            }
            $statistics[$month]['confirmed_bookings'] = $bookings;
            $statistics[$month]['unconfirmed_bookings'] = $unconfirmed;
            $statistics[$month]['placements'] = $monthPlacements;
            $statistics[$month]['videos'] = $monthVideos;
            $statistics[$month]['requests'] = $monthRequests;
            $month = $nextMonth;
            $bookings = $unconfirmed = $monthPlacements = $monthVideos = $monthRequests = 0 ;
        }
        return $this->response->setContent($this->twig->render('admin/statistics/index.twig', [
            'title' => 'Montly Statistics',
            'statistics' => $statistics,
        ]));
    }
}
