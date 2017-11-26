<?php

namespace CartBooking\Application\Api\Transformer;

use CartBooking\Model\Publisher\Publisher;
use League\Fractal\TransformerAbstract;

class PublisherTransformer extends TransformerAbstract
{
    public function __construct()
    {
        $this->availableIncludes = [
            'bookings'
        ];
    }

    public function transform(Publisher $publisher)
    {
        return [
            'id' => $publisher->getId(),
            'email' => $publisher->getEmail(),
            'fullName' => $publisher->getFullName()
        ];
    }

    public function includeBookings(Publisher $publisher)
    {
        return $this->collection($publisher->getBookings(), new BookingTransformer());
    }
}
