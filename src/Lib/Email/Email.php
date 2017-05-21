<?php
/**
 * Created by PhpStorm.
 * Publisher: sebastian.machuca
 * Date: 25/4/17
 * Time: 3:24 PM
 */

namespace CartBooking\Lib\Email;


class Email
{
    /**
     * @var string
     */
    private $email;

    public function __construct(string $email)
    {
        $filteredEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
        if ($filteredEmail === false || $filteredEmail !== $email) {
            throw new \InvalidArgumentException('No valid communication supplied');
        }
        $this->email = $email;
    }

    public function __toString()
    {
        return $this->email;
    }
}
