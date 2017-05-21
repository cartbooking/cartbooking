<?php

namespace CartBooking\Publisher;

use CartBooking\Lib\Db\Db;

class PioneerRepository
{
    /**
     * @var Db
     */
    private $db;
    /**
     * @var PioneerHydrator
     */
    private $pioneerHydrator;

    public function __construct(Db $db, PioneerHydrator $pioneerHydrator)
    {
        $this->db = $db;
        $this->pioneerHydrator = $pioneerHydrator;
    }

    /**
     * @param string $gender
     * @return Pioneer[]|\Generator
     */
    public function findByGender(string $gender)
    {
        $query = "SELECT * FROM pioneers WHERE gender = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $gender);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            yield $this->pioneerHydrator->hydrate($row);
        }
    }

    /**
     * @return \Generator|Pioneer[]
     */
    public function findAll()
    {
        $query = "SELECT * FROM pioneers";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            yield $this->pioneerHydrator->hydrate($row);
        }
    }

    /**
     * @param int $id
     * @return \CartBooking\Publisher\Pioneer|null
     */
    public function findById(int $id)
    {
        $query = "SELECT * FROM pioneers WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null;
        }
        return $this->pioneerHydrator->hydrate($result->fetch_array(MYSQLI_ASSOC));
    }

    /**
     * @param int $phone
     * @return Pioneer|null
     */
    public function findByPhone(int $phone)
    {
        $query = "SELECT * FROM pioneers WHERE phone = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null;
        }
        return $this->pioneerHydrator->hydrate($result->fetch_array(MYSQLI_ASSOC));
    }

    /**
     * @param $name
     * @return Pioneer[]
     */
    public function findByName($name)
    {
        $query = "SELECT * FROM pioneers WHERE first_name = ? or last_name = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $name, $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $pioneers = [];
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $pioneers[] = $this->pioneerHydrator->hydrate($row);
        }
        return $pioneers;
    }

    /**
     * @return Pioneer[]|\Generator
     */
    public function findActive()
    {
        $query = "SELECT * FROM pioneers
                        WHERE inactive != ? and inactive != ? and email != ?
                        ORDER BY last_name, first_name";
        $stmt = $this->db->prepare($query);
        $inactive = 'y';
        $deactivated = 'd';
        $email = '';
        $stmt->bind_param('sss', $inactive, $deactivated, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            yield;
        }
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            yield $this->pioneerHydrator->hydrate($row);
        }
    }
}
