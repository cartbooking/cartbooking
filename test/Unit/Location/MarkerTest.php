<?php

namespace Test\Unit\Location;

use CartBooking\Model\Location\Coordinate;
use CartBooking\Model\Location\Coordinate\Latitude;
use CartBooking\Model\Location\Coordinate\Longitude;
use CartBooking\Model\Location\Marker;
use CartBooking\Model\Location\Marker\Color;
use CartBooking\Model\Location\Marker\Label;
use Test\AutoMockingTest;

class MarkerTest extends AutoMockingTest
{
    public function testMarker()
    {
        $marker = new Marker(
            new Color('red'),
            new Label('A'),
            new Coordinate(new Latitude(0.0), new Longitude(0.0))
        );
        static::assertSame('color:red|label:A|0,0', (string)$marker);
    }
}
