<?php

namespace CartBooking\Infrastructure\Persistence\Doctrine\Type;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class DateTimeImmutableType extends Type
{
    const DATE_TIME_IMMUTABLE = 'date_time_immutable';
    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array $fieldDeclaration The field declaration.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'date_time_immutable';
    }

    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @param DateTimeImmutable                                     $value    The value to convert.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The database representation of the value.
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value->format(DATE_ATOM);
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param mixed                                     $value    The value to convert.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return DateTimeImmutable The PHP representation of the value.
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat('H:i:s|', $value, new DateTimeZone('UTC'));
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     *
     * @todo Needed?
     */
    public function getName()
    {
        return self::DATE_TIME_IMMUTABLE;
    }
}
