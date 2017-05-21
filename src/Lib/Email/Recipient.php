<?php
namespace CartBooking\Lib\Email;

class Recipient
{
    /**
     * @var Name
     */
    private $name;
    /**
     * @var Email
     */
    private $email;

    public function __construct(Name $name, Email $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function formatted()
    {
        return $this->name->getFirstName() . ' ' . $this->name->getLastName() . ' <' . $this->email . '>';
    }
}
