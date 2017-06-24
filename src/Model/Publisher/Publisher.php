<?php

namespace CartBooking\Model\Publisher;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Publisher
{
    /** @var int */
    private $id;
    /** @var string */
    private $preferredName;
    /** @var string */
    private $fullName;
    /** @var string */
    private $gender;

    private $phone;

    private $email;

    private $status;
    /** @var string */
    private $password;

    /** @var  bool */
    private $inactive;

    /** @var ArrayCollection */
    private $relatives;

    public function __construct()
    {
        $this->relatives = new ArrayCollection();
        $this->inactive = false;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    public function isMale()
    {
        return $this->getGender() === 'm';
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return bool
     */
    public function isInactive(): bool
    {
        return $this->inactive;
    }

    /**
     * @param bool $inactive
     */
    public function setInactive(bool $inactive)
    {
        $this->inactive = $inactive;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /** @return Publisher[] */
    public function getRelatives(): Collection
    {
        return $this->relatives;
    }

    /**
     * @param int[] $relatives
     */
    public function addRelatives(array $relatives)
    {
        foreach ($relatives as $relative) {
            $this->relatives->add($relative);
        }
    }

    public function isRelativeTo(Publisher $pioneer): bool
    {
        return $this->relatives->contains($pioneer);
    }

    /**
     * @return string
     */
    public function getPreferredName(): string
    {
        return $this->preferredName;
    }

    /**
     * @param string $preferredName
     */
    public function setPreferredName(string $preferredName)
    {
        $this->preferredName = $preferredName;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName(string $fullName)
    {
        $this->fullName = $fullName;
    }
}
