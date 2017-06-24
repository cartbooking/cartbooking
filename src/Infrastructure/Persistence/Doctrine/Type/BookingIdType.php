<?php

namespace CartBooking\Infrastructure\Persistence\Doctrine\Type;

use CartBooking\Model\Booking\BookingId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class BookingIdType extends GuidType
{
    const BOOKING_ID = 'BookingId';

    public function getName()
    {
        return 'BookingId';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new BookingId((string)$value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return (string)$value;
    }
}
