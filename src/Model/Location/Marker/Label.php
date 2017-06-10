<?php

namespace CartBooking\Model\Location\Marker;

class Label
{
    /** @var string */
    private $label;

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public function __toString()
    {
        return 'label:' . $this->label;
    }
}
