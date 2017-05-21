<?php

namespace CartBooking\Lib\Db;

class Host
{
    /**
     * @var string
     */
    private $host;

    public function __construct(string $host)
    {
        $this->host = $host;
    }

    public function __toString()
    {
        return $this->host;
    }
}
