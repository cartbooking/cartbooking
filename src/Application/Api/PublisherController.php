<?php

namespace CartBooking\Application\Api;

use CartBooking\Application\Api\Transformer\PublisherTransformer;
use CartBooking\Application\WebPublisherService;
use CartBooking\Model\Booking\BookingRepository;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Symfony\Component\HttpFoundation\JsonResponse;

class PublisherController
{
    /** @var Manager */
    private $manager;
    /** @var WebPublisherService */
    private $publisherService;
    /** @var BookingRepository */
    private $bookingRepository;

    public function __construct(
        WebPublisherService $publisherService,
        BookingRepository $bookingRepository,
        Manager $manager
    ) {
        $this->manager = $manager;
        $this->publisherService = $publisherService;
        $this->bookingRepository = $bookingRepository;
    }

    public function getAction(): JsonResponse
    {
        $publisher = $this->publisherService->getCurrentUser();
        $publisher->setBookings($this->bookingRepository->findByPublisherId($publisher->getId()));
        return new JsonResponse(
            $this->manager->createData(
                new Item($publisher, new PublisherTransformer())
            )
        );
    }
}
