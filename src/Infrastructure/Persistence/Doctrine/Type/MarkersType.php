<?php

namespace CartBooking\Infrastructure\Persistence\Doctrine\Type;

use CartBooking\Location\Coordinate;
use CartBooking\Location\Coordinate\Latitude;
use CartBooking\Location\Coordinate\Longitude;
use CartBooking\Location\Marker;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class MarkersType extends Type
{
    const MARKERS = 'markers';
    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array $fieldDeclaration The field declaration.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'markers';
    }

    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @param mixed                                     $value    The value to convert.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The database representation of the value.
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return json_encode($value);
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param mixed                                     $value    The value to convert.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return Marker[] The PHP representation of the value.
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): array
    {
        $markers = [];
        $markersData = json_decode($value, true);
        if (is_array($markersData)) {
            foreach ($markersData as $markersDatum) {
                list($latitude, $longitude) = explode(',', $markersDatum['coordinates']);
                $markers[] = new Marker(
                    new Marker\Color($markersDatum['color']),
                    new Marker\Label($markersDatum['label']),
                    new Coordinate(new Latitude($latitude), new Longitude($longitude))
                );
            }
        }
        return $markers;
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName(): string
    {
        return self::MARKERS;
    }
}
