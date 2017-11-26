<?php

namespace CartBooking\Application\Api;

use CartBooking\Application\Api\Transformer\BookingTransformer;
use CartBooking\Model\Booking\BookingService;
use Crell\ApiProblem\ApiProblem;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController
{
    /** @var BookingService */
    private $bookingService;
    /** @var Manager */
    private $manager;
    /** @var Request */
    private $request;

    public function __construct(BookingService $bookingService, Manager $manager, Request $request)
    {
        $this->bookingService = $bookingService;
        $this->manager = $manager;
        $this->request = $request;
    }

    public function getAction(): JsonResponse
    {
        $from = \DateTimeImmutable::createFromFormat(DATE_ATOM, $this->request->get('from'));
        $to = \DateTimeImmutable::createFromFormat(DATE_ATOM, $this->request->get('to'));
        if ($from > $to) {
            $problem = new ApiProblem();
            $problem->setStatus(Response::HTTP_BAD_REQUEST);
            $problem->setTitle('Incorrect Dates');
            $problem->setDetail("Date 'from' must be previous to date 'to'");
            return new JsonResponse($problem->asJson(), $problem->getStatus(), [], true);
        }
        return new JsonResponse($this->manager->createData(new Collection(
            $this->bookingService->findByRange($from, $to),
            new BookingTransformer()
        ))->toJson(), Response::HTTP_OK, [], true);
    }
}
