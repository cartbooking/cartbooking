<?php

namespace CartBooking\Model\Booking;

use CartBooking\Model\Publisher\Publisher;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;

class Booking
{
    /** @var BookingId */
    private $id;
    /** @var int */
    private $pioneerId = 0;
    /** @var int */
    private $pioneerBId = 0;
    /** @var int */
    private $shiftId;
    /** @var DateTimeImmutable */
    private $date;
    /** @var bool */
    private $confirmed = false;
    /** @var bool */
    private $isFull = false;
    /** @var bool If the Booking has been recorded the results */
    private $recorded = false;
    /** @var int */
    private $placements = 0;
    /** @var int */
    private $videos = 0;
    /** @var int  */
    private $requests = 0;
    /** @var int */
    private $overseerId = 0;
    /** @var string */
    private $comments = '';
    /** @var bool */
    private $seen = false;
    /** @var ArrayCollection */
    private $publishers;

    public function __construct(BookingId $id, int $shiftId, DateTimeImmutable $date)
    {
        $this->shiftId = $shiftId;
        $this->date = $date;
        $this->publishers = new ArrayCollection();
        $this->id = $id;
    }

    /**
     * @return BookingId
     */
    public function getId(): BookingId
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPioneerId(): int
    {
        return $this->pioneerId;
    }

    /**
     * @return int
     */
    public function getOverseerId(): int
    {
        return $this->overseerId;
    }

    public function hasOverseer()
    {
        return $this->overseerId > 0;
    }

    /**
     * @param int $overseerId
     * @internal
     */
    public function setOverseerId(int $overseerId)
    {
        $this->overseerId = $overseerId;
    }

    /**
     * @return int
     */
    public function getShiftId(): int
    {
        return $this->shiftId;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    /**
     * @param bool $confirmed
     */
    public function setConfirmed(bool $confirmed)
    {
        $this->confirmed = $confirmed;
    }

    /**
     * @return bool
     */
    public function isRecorded(): bool
    {
        return new DateTimeImmutable('now') > $this->date  && $this->recorded;
    }

    /**
     * @param bool $recorded
     */
    public function setRecorded(bool $recorded)
    {
        $this->recorded = $recorded;
    }

    /**
     * @return int
     */
    public function getPioneerBId(): int
    {
        return $this->pioneerBId;
    }


    /**
     * @return int
     */
    public function getPlacements(): int
    {
        return $this->placements;
    }

    /**
     * @param int $placements
     */
    public function setPlacements(int $placements)
    {
        $this->placements = $placements;
    }

    /**
     * @return int
     */
    public function getVideos(): int
    {
        return $this->videos;
    }

    /**
     * @param int $videos
     */
    public function setVideos(int $videos)
    {
        $this->videos = $videos;
    }

    /**
     * @return int
     */
    public function getRequests(): int
    {
        return $this->requests;
    }

    /**
     * @param int $requests
     */
    public function setRequests(int $requests)
    {
        $this->requests = $requests;
    }

    /**
     * @return string
     */
    public function getComments(): string
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     */
    public function setComments(string $comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return bool
     */
    public function isFull(): bool
    {
        return $this->isFull;
    }

    /**
     * @param bool $isFull
     */
    public function setIsFull(bool $isFull)
    {
        $this->isFull = $isFull;
    }

    /**
     * @return bool
     */
    public function hasBeenSeen(): bool
    {
        return $this->seen;
    }

    /**
     * @param bool $seen
     */
    public function setSeen(bool $seen)
    {
        $this->seen = $seen;
    }

    public function dismiss()
    {
        $this->seen = true;
    }

    /**
     * @param int $pioneerId
     * @internal
     */
    public function setPioneerId(int $pioneerId)
    {
        $this->pioneerId = $pioneerId;
    }

    /**
     * @param Publisher[] $publishers
     */
    public function setPublishers(array $publishers)
    {
        $this->overseerId = 0;
        foreach ($publishers as $publisher) {
            if ($this->overseerId === 0 && $publisher->isMale()) {
                $this->overseerId = $publisher->getId();
            }
        }
        $this->publishers = $publishers;
        $this->updateStatus($publishers);
    }

    public function getPublishersIds(): array
    {
        return $this->publishers->map(function (Publisher $publisher) {
            return $publisher->getId();
        })->toArray();
    }

    /**
     * @return ArrayCollection|Publisher[]
     */
    public function getPublishers()
    {
        return $this->publishers;
    }

    /**
     * @param Publisher[] $publishers
     */
    private function updateStatus(array $publishers)
    {
        $this->confirmed = false;
        if (count($publishers) > 2) {
            $this->confirmed = true;
            return;
        }
        if (count($publishers) === 2 && $publishers[0]->isRelativeTo($publishers[1])) {
            $this->confirmed = true;
            return;
        }
        if (count($publishers) === 2 && count(array_filter($publishers, function (Publisher $publisher) { return $publisher->isMale();})) === 1) {
            $this->confirmed = false;
            return;
        }
    }

}
