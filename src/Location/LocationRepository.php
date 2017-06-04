<?php
/**
 * Created by PhpStorm.
 * User: sebastian.machuca
 * Date: 30/4/17
 * Time: 10:23 PM
 */

namespace CartBooking\Location;


use CartBooking\Lib\Db\Db;
use Doctrine\ORM\EntityManager;

class LocationRepository
{
    /**
     * @var Db
     */
    private $db;
    /**
     * @var LocationHydrator
     */
    private $locationHydrator;
    /** @var EntityManager */
    private $manager;

    public function __construct(Db $db, LocationHydrator $locationHydrator, EntityManager $manager)
    {
        $this->db = $db;
        $this->locationHydrator = $locationHydrator;
        $this->manager = $manager;
    }

    /**
     * @return \Generator|Location[]
     */
    public function findAll()
    {
        $query = "SELECT * FROM locations";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            yield $this->locationHydrator->hydrate($row);
        }
    }

    public function findById(int $id)
    {
        $query = "SELECT * FROM locations WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result !== false) {
            return $this->locationHydrator->hydrate($result->fetch_array(MYSQLI_ASSOC));
        }
        return null;
    }
}
