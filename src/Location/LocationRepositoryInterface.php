<?php

namespace CartBooking\Location;

interface LocationRepositoryInterface
{
    /**
     * @return \Generator|Location[]
     */
    public function findAll();

    public function findById(int $id);
}
