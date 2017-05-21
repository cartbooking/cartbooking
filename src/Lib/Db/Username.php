<?php

namespace CartBooking\Lib\Db;

class Username
{
    /**
     * @var string
     */
    private $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function __toString()
    {
        return $this->username;
    }
}
