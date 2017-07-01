<?php

namespace CartBooking\Model\Publisher\Command;

class UpdatePasswordCommand
{
    /** @var string */
    private $password;
    /** @var int */
    private $publisherId;

    public function __construct(int $publisherId, string $password)
    {
        $this->password = $password;
        $this->publisherId = $publisherId;
    }

    public function getPassword()
    {
        return password_hash($this->password, PASSWORD_BCRYPT);
    }

    /**
     * @return int
     */
    public function getPublisherId(): int
    {
        return $this->publisherId;
    }
}
