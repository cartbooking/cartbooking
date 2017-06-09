<?php

namespace CartBooking\Publisher;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Publisher
{
    /**
     * @var int
     */
    private $id;

    private $firstName;

    private $lastName;

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

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->relatives = new ArrayCollection();
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
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
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
}
