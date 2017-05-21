<?php

namespace CartBooking\Lib\Db;

class Password
{
    /**
     * @var string
     */
    private $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    public function __toString()
    {
        return $this->password;
    }
}
