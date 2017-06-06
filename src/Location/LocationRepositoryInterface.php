<?php

namespace CartBooking\Location;

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
