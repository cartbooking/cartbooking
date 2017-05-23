<?php

namespace CartBooking\Publisher;

use CartBooking\Lib\Db\Db;

class PublisherRepository
{
    /**
     * @var Db
     */
    private $db;
    /**
     * @var PublisherHydrator
     */
    private $pioneerHydrator;

    public function __construct(Db $db, PublisherHydrator $pioneerHydrator)
    {
        $this->db = $db;
        $this->pioneerHydrator = $pioneerHydrator;
    }

    /**
     * @param string $gender
     * @return Publisher[]|\Generator
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
     * @return \Generator|Publisher[]
     */
    public function findAll()
    {
        $query = "SELECT * FROM pioneers";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $query = "SELECT * FROM relationships WHERE publisher_id_1 = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $row['id']);
            $stmt->execute();
            $relativesResult = $stmt->get_result();
            if($relativesResult->num_rows > 0) {
                $row['relatives'] = $relativesResult->fetch_all(MYSQLI_ASSOC);
            }
            yield $this->pioneerHydrator->hydrate($row);
        }
    }

    /**
     * @param int $id
     * @return \CartBooking\Publisher\Publisher|null
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
        $data = $result->fetch_array(MYSQLI_ASSOC);
        $query = "SELECT * FROM relationships WHERE publisher_id_1 = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $relativesResult = $stmt->get_result();
        if($relativesResult->num_rows > 0) {
            $data['relatives'] = $relativesResult->fetch_all(MYSQLI_ASSOC);
        }

        return $this->pioneerHydrator->hydrate($data);
    }

    /**
     * @param int $phone
     * @return Publisher|null
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
     * @return Publisher[]
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
     * @return Publisher[]|\Generator
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
