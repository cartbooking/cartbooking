<?php

namespace CartBooking\Model\Publisher\Command;

class UpdatePublisherCommand
{
    /** @var int */
    private $publisherId;
    /** @var string */
    private $name;
    /** @var string */
    private $phone;
    /** @var string */
    private $email;

    public function __construct(int $publisherId, string $name, string $phone, string $email)
    {
        $this->publisherId = $publisherId;
        $this->name = $name;
        $this->phone = $phone;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException();
        }
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getPublisherId(): int
    {
        return $this->publisherId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

}
