<?php
/**
 * Created by PhpStorm.
 * User: sebastian.machuca
 * Date: 30/4/17
 * Time: 10:01 PM
 */

namespace CartBooking\Model\Shift;


use DateTimeImmutable;

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
     * @var DateTimeImmutable
     */
    private $startTime;
    /**
     * @var DateTimeImmutable
     */
    private $endTime;
    /**
     * @var int
     */
    private $id;

    public function __construct(int $id, int $locationId, int $day, DateTimeImmutable $startTime, DateTimeImmutable $endTime)
    {
        $this->locationId = $locationId;
        $this->day = $day;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->id = $id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getEndTime(): DateTimeImmutable
    {
        return $this->endTime;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getStartTime(): DateTimeImmutable
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
