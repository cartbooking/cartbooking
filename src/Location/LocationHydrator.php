<?php
/**
 * Created by PhpStorm.
 * User: sebastian.machuca
 * Date: 30/4/17
 * Time: 10:25 PM
 */

namespace CartBooking\Location;


use CartBooking\Location\Marker\Color;
use CartBooking\Location\Marker\Coordinate;
use CartBooking\Location\Marker\Label;

class LocationHydrator
{
    public function hydrate(array $row)
    {
        $location = new Location($row['id'], $row['name']);
        $location->setCentre($row['centre']);
        $location->setDescription($row['description']);
        $this->setMarkers($row, $location);
        $location->setPath($row['path']);
        $location->setVolunteers($row['volunteers']);
        $location->setZoom($row['zoom']);
        $location->setCapacity($row['capacity']);
        return $location;
    }

    /**
     * @param array $row
     * @param $location
     */
    protected function setMarkers(array $row, Location $location)
    {
        $markers = [];
        $jsonMarker = json_decode($row['markers'], true);
        if (is_array($jsonMarker)) {
            foreach ($jsonMarker as $marker) {
                list($latitude, $longitude) = explode(',', $marker['coordinates']);
                $markers[] = new Marker(
                    new Color($marker['color']),
                    new Label($marker['label']),
                    new Coordinate((float)$latitude, (float) $longitude)
                );
            }
        }
        $location->setMarkers($markers);
    }
}
