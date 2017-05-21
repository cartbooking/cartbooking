<?php

namespace CartBooking\Lib\Db;

use CartBooking\Lib\Email\Email as RecepientEmail;
use CartBooking\Lib\Email\Name as RecepientName;
use CartBooking\Lib\Email\Recipient;
use InvalidArgumentException;

class Db
{
    private $connection;

    public function __construct(Host $host, Name $name, Username $username, Password $password)
    {
        $this->connection = mysqli_connect((string)$host, (string)$username, (string)$password, (string)$name);
        if (mysqli_connect_errno()) {
            throw new InvalidArgumentException("Failed to connect to MySQL: " . mysqli_connect_error());
        }
    }

    public function prepare(string $statement)
    {
        return $this->connection->prepare($statement);
    }

    public function close()
    {
        return $this->connection->close();
    }

    public function findPioneerEmailInformation(int $id)
    {
        $emailInformationQuery = "SELECT first_name, last_name, communication FROM pioneers WHERE id = ?";
        $stmt = $this->prepare($emailInformationQuery);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($firstName, $lastName, $email);
        $stmt->fetch();
        $stmt->close();
        return new Recipient(new RecepientName($firstName, $lastName), new RecepientEmail($email));
    }

    public function findMalePioneers()
    {

    }
}
