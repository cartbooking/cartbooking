<?php

namespace CartBooking\Model\Location;

interface LocationRepositoryInterface
{
    /**
     * @return Location[]
     */
    public function findAll();

    /**
     * @param int $id
     * @return Location
     */
    public function findById(int $id);
}
