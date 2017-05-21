<?php
/**
 * Created by PhpStorm.
 * User: sebastian.machuca
 * Date: 30/4/17
 * Time: 8:43 PM
 */

namespace CartBooking\Booking;


use CartBooking\Lib\Db\Db;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

class BookingRepository
{
    /**
     * @var Db
     */
    private $db;
    /**
     * @var BookingHydrator
     */
    private $bookingHydrator;

    public function __construct(Db $db, BookingHydrator $bookingHydrator)
    {
        $this->db = $db;
        $this->bookingHydrator = $bookingHydrator;
    }

    /**
     * @param int $id
     * @return Booking|null
     */
    public function findById(int $id)
    {
        $query = 'SELECT * FROM bookings WHERE id = ?';
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null;
        }
        return $this->bookingHydrator->hydrate($result->fetch_assoc());
    }

    public function findByPublisherId(int $publisherId)
    {
        $query = 'SELECT * FROM bookings WHERE overseer_id = ? OR pioneer_id = ? OR pioneer_b_id = ?';
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iii', $publisherId, $publisherId, $publisherId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return [];
        }
        $bookings = [];
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $bookings[] =  $this->bookingHydrator->hydrate($row);
        }
        return $bookings;
    }

    /**
     * @param DateTimeImmutable $date
     * @return \Generator|Booking[]
     */
    public function findNonRecordedBookingsOlderThan(DateTimeImmutable $date)
    {
        $find_bookings = "SELECT * 
            FROM bookings 
            WHERE date <= ? AND confirmed = 'y' AND recorded <> 'y' and overseer_id <> 0 
            ORDER BY overseer_id, date";
        $mysqliStmt = $this->db->prepare($find_bookings);
        $dateFormatted = $date->format(DateTime::ATOM);
        $mysqliStmt->bind_param('s', $dateFormatted);
        $mysqliStmt->execute();
        $result = $mysqliStmt->get_result();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            yield $this->bookingHydrator->hydrate($row);
        }
    }

    /**
     * @param int $userId
     * @return \Generator|Booking[]
     */
    public function findPendingBookingsForUser(int $userId)
    {
        $recorded = 'y';
        $confirmed = 'y';
        $today = date('Y-m-d', strtotime("today"));
        $pendingBookings = "SELECT * 
                  FROM bookings 
                  WHERE (recorded != ? or recorded IS null) AND (overseer_id = ? OR pioneer_id = ? OR pioneer_b_id = ?) AND date <= ? AND confirmed = ? 
                  ORDER BY date, shift_id";
        $stmt = $this->db->prepare($pendingBookings);
        $stmt->bind_param('siiiss', $recorded, $userId, $userId, $userId, $today, $confirmed);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            yield $this->bookingHydrator->hydrate($row);
        }
    }

    /**
     * @param int $publisherId
     * @param DateTimeInterface $date
     * @return Booking[]
     */
    public function findByPublisherIdAndDate(int $publisherId, DateTimeInterface $date): array
    {
        $bookings = "SELECT * FROM bookings 
                    WHERE date = ? AND (overseer_id = ? OR pioneer_id = ? OR pioneer_b_id = ?)";
        $stmt = $this->db->prepare($bookings);
        $dateFormatted = $date->format('Y-m-d');
        $stmt->bind_param('siii', $dateFormatted, $publisherId, $publisherId, $publisherId);
        $stmt->execute();
        $result = $stmt->get_result();
        $bookings = [];
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $bookings[] = $this->bookingHydrator->hydrate($row);
        }
        return $bookings;
    }

    public function findByShiftAndDate(int $shiftId, DateTimeInterface $date)
    {
        $query = "SELECT * FROM bookings WHERE shift_id = ? AND date = ?";
        $stmt = $this->db->prepare($query);
        $dateFormatted = $date->format('Y-m-d');
        $stmt->bind_param('is', $shiftId, $dateFormatted);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null;
        }
        return $this->bookingHydrator->hydrate($result->fetch_array(MYSQLI_ASSOC));
    }

    /**
     * @param DateTimeImmutable $fromDate
     * @param DateTimeImmutable $toDate
     * @return \Generator|Booking[]
     */
    public function findByDateBetween(DateTimeImmutable $fromDate, DateTimeImmutable $toDate)
    {
        $query = "SELECT * FROM bookings WHERE date >= ? AND date < ? ORDER BY date";
        $stmt = $this->db->prepare($query);
        $from = $fromDate->format('Y-m-d');
        $to = $toDate->format('Y-m-d');
        $stmt->bind_param('ss', $from, $to);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            yield $this->bookingHydrator->hydrate($row);
        }
    }

    /**
     * @return \Generator|Booking[]
     */
    public function findUnseenBookingsComments()
    {
        $shift_info = "SELECT * FROM bookings WHERE comments != ? and experience = ? ORDER BY date DESC";
        $stmt = $this->db->prepare($shift_info);
        $comments = '';
        $experience = 'y';
        $stmt->bind_param('ss', $comments, $experience);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            yield $this->bookingHydrator->hydrate($row);
        }
    }

    public function save(Booking $booking)
    {
        $update = 'UPDATE bookings SET 
                  shift_id = ?, date = ?, overseer_id = ?, pioneer_id = ?, pioneer_b_id = ?, confirmed = ?, full = ?,
                  recorded = ?, placements = ?, videos = ?, requests = ?, comments = ?, experience = ?
                  WHERE id = ?';
        $stmt = $this->db->prepare($update);
        $format = $booking->getDate()->format(DATE_ATOM);
        $confirmed = $booking->isConfirmed() ? 'y' : 'n';
        $full = $booking->isFull() ? 'y' : 'n';
        $recorded = $booking->isRecorded() ? 'y' : 'n';
        $experience = $booking->isExperience() ? 'y' : 'n';
        $stmt->bind_param(
            'isiiisssiiissi',
            $booking->getShiftId(),
            $format,
            $booking->getOverseerId(),
            $booking->getPioneerId(),
            $booking->getPioneerBId(),
            $confirmed,
            $full,
            $recorded,
            $booking->getPlacements(),
            $booking->getVideos(),
            $booking->getRequests(),
            $booking->getComments(),
            $experience,
            $booking->getId()
        );
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * @param DateTimeImmutable $fromDate
     * @param DateTimeImmutable $toDate
     * @return \Generator|Booking[]
     */
    public function findBookingsNeedingOverseerBetween(DateTimeImmutable $fromDate, DateTimeImmutable $toDate)
    {
        $query = "SELECT * FROM bookings WHERE date > ? AND date <= ? AND overseer_id = 0 ORDER BY date, shift_id";
        $stmt = $this->db->prepare($query);
        $from = $fromDate->format('Y-m-d');
        $to = $toDate->format('Y-m-d');
        $stmt->bind_param('ss', $from, $to);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            yield $this->bookingHydrator->hydrate($row);
        }
    }

    /**
     * @param DateTimeImmutable $fromDate
     * @param DateTimeImmutable $toDate
     * @return \Generator|Booking[]
     */
    public function findBookingsNeedingVolunteersBetween(DateTimeImmutable $fromDate, DateTimeImmutable $toDate)
    {
        $query = "SELECT * FROM bookings WHERE date > ? AND date <= ? AND confirmed = 'y' ORDER BY date, shift_id";
        $stmt = $this->db->prepare($query);
        $from = $fromDate->format('Y-m-d');
        $to = $toDate->format('Y-m-d');
        $stmt->bind_param('ss', $from, $to);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            yield $this->bookingHydrator->hydrate($row);
        }
    }
}
