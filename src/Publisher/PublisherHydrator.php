<?php
/**
 * Created by PhpStorm.
 * Publisher: sebastian.machuca
 * Date: 29/4/17
 * Time: 5:45 PM
 */

namespace CartBooking\Publisher;


class PublisherHydrator
{
    public function hydrate(array $row)
    {
        $pioneer = new Publisher($row['id']);
        $pioneer->setFirstName($row['first_name']);
        $pioneer->setLastName($row['last_name']);
        $pioneer->setGender($row['gender']);
        $pioneer->setPhone($row['phone']);
        $pioneer->setEmail($row['email']);
        $pioneer->setCongregation($row['congregation']);
//        $pioneer->setStatus($row['status']);
        $pioneer->setPassword((string)$row['password']);
        $pioneer->setInactive($row['inactive'] === 'y' || $row['inactive'] === 1);
        if (isset($row['relatives'])) {
            $pioneer->setRelatives(array_map(function (array $row){
                return $row['publisher_id_2'];
            }, $row['relatives']));
        }
        return $pioneer;
    }
}
