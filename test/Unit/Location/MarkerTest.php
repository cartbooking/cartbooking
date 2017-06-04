<?php

namespace Test\Unit\Location;

use CartBooking\Location\Marker;
use CartBooking\Location\Marker\Color;
use CartBooking\Location\Marker\Coordinate;
use CartBooking\Location\Marker\Label;
use Test\AutoMockingTest;

class MarkerTest extends AutoMockingTest
{
    public function testMarker()
    {
        $marker = new Marker(new Color('red'), new Label('A'), new Coordinate(0.0, 0.0));
        static::assertSame('color:red|label:A|0,0', (string)$marker);
    }
}
