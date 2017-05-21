<?php
/**
 * Created by PhpStorm.
 * User: sebastian.machuca
 * Date: 30/4/17
 * Time: 10:01 PM
 */

namespace CartBooking\Shift;


class Shift
{
    /**
     * @var int
     */
    private $locationId;
    /**
     * @var int
     */
    private $day;
    /**
     * @var string
     */
    private $startTime;
    /**
     * @var string
     */
    private $endTime;
    /**
     * @var int
     */
    private $id;

    public function __construct(int $id, int $locationId, int $day, string $startTime, string $endTime)
    {
        $this->locationId = $locationId;
        $this->day = $day;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEndTime(): string
    {
        return $this->endTime;
    }

    /**
     * @return string
     */
    public function getStartTime(): string
    {
        return $this->startTime;
    }

    /**
     * @return int
     */
    public function getDay(): int
    {
        return $this->day;
    }

    /**
     * @return int
     */
    public function getLocationId(): int
    {
        return $this->locationId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
