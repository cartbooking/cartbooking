<?php

namespace CartBooking\Model\Publisher\Command;

class AddPublisherCommand
{
    /** @var string */
    private $preferredName;
    /** @var string */
    private $fullName;
    /** @var string */
    private $email;
    /** @var string */
    private $phone;
    /** @var string */
    private $gender;

    public function __construct(string $preferredName, string $fullName, string $email, string $phone, string $gender)
    {
        $this->preferredName = $preferredName;
        $this->fullName = $fullName;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException();
        }
        $this->email = $email;
        $this->phone = $phone;
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getPreferredName(): string
    {
        return $this->preferredName;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
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
    public function getGender(): string
    {
        return $this->gender;
    }
}
