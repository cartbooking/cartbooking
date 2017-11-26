<?php

namespace CartBooking\Application\Api\Transformer;

use CartBooking\Model\Booking\Booking;
use CartBooking\Model\Publisher\Publisher;
use League\Fractal\TransformerAbstract;

class BookingTransformer extends TransformerAbstract
{
    public function __construct()
    {
        $this->availableIncludes = [
            'publishers'
        ];
    }

    public function transform(Booking $booking)
    {
        return [
            'id' => $booking->getId(),
            'date' => $booking->getDate()->format(DATE_ATOM),
            'publishers' => array_map(function (Publisher $booking) {
                return $booking->getId();
            }, $booking->getPublishers()->toArray()),
            'confirmed' => $booking->isConfirmed()
        ];
    }

    public function includePublishers(Booking $booking)
    {
        return $this->collection($booking->getPublishers(), new PublisherTransformer());
    }
}
