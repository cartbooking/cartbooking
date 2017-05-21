<?php
/**
 * Created by PhpStorm.
 * Publisher: sebastian.machuca
 * Date: 29/4/17
 * Time: 5:45 PM
 */

namespace CartBooking\Publisher;


class PioneerHydrator
{
    public function hydrate(array $row)
    {
        $pioneer = new Pioneer($row['id']);
        $pioneer->setFirstName($row['first_name']);
        $pioneer->setLastName($row['last_name']);
        $pioneer->setGender($row['gender']);
        $pioneer->setPhone($row['phone']);
        $pioneer->setEmail($row['email']);
        $pioneer->setCongregation($row['congregation']);
//        $pioneer->setStatus($row['status']);
        $pioneer->setPassword((string)$row['password']);
        $pioneer->setInactive($row['inactive'] === 'y' || $row['inactive'] === 1);
        return $pioneer;
    }
}
