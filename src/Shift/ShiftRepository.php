<?php

namespace CartBooking\Shift;

use CartBooking\Lib\Db\Db;

class ShiftRepository
{
    /**
     * @var Db
     */
    private $db;
    /** @var ShiftHydrator */
    private $hydrator;

    public function __construct(Db $db, ShiftHydrator $hydrator)
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
    }

    /**
     * @param int $id
     * @return Shift
     */
    public function findById(int $id)
    {
        $query = "SELECT * FROM shifts WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_ASSOC);
        return $this->hydrator->hydrate($row);
    }

    /**
     * @param int $day
     * @param int $locationId
     * @return Shift[]
     */
    public function findByDayAndLocation(int $day, int $locationId): array
    {
        $query = 'SELECT * FROM shifts WHERE day = ? AND location_id = ? ORDER BY start_time';
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $day, $locationId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return [];
        }
        $shifts = [];
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $shifts[] = $this->hydrator->hydrate($row);
        }
        return $shifts;
    }
}
