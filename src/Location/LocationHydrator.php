<?php
/**
 * Created by PhpStorm.
 * User: sebastian.machuca
 * Date: 30/4/17
 * Time: 10:25 PM
 */

namespace CartBooking\Location;


class LocationHydrator
{
    public function hydrate(array $row)
    {
        $location = new Location($row['id'], $row['name']);
        $location->setCentre($row['centre']);
        $location->setDescription($row['description']);
        $location->setMarkers($row['markers']);
        $location->setPath($row['path']);
        $location->setVolunteers($row['volunteers']);
        $location->setZoom($row['zoom']);
        $location->setCapacity($row['capacity']);
        return $location;
    }
}
