<?php

namespace CartBooking\Model\Booking;

class BookingHydrator
{
    public function hydrate(array $row)
    {
        $booking = new Booking($row['id'], $row['shift_id'], new \DateTimeImmutable($row['date']));
        $booking->setConfirmed($row['confirmed'] === 'y');
        $booking->setRecorded($row['recorded'] === 'y');
        $booking->setPlacements((int)$row['placements']);
        $booking->setVideos((int)$row['videos']);
        $booking->setRequests((int)$row['requests']);
        if ($row['pioneer_id'] > 0) {
            $booking->setPioneerId($row['pioneer_id']);
        }
        if ($row['overseer_id'] > 0) {
            $booking->setOverseerId($row['overseer_id']);
        }
        if ($row['pioneer_b_id'] > 0) {
            $booking->setPioneerBId($row['pioneer_b_id']);
        }
        $booking->setIsFull($row['full'] === 'y');
        $booking->setExperience($row['experience'] === 'y');
        $booking->setComments((string)$row['comments']);
        return $booking;
    }

    public function dehydrate(Booking $booking)
    {
        return [
            'id' => $booking->getId(),
            'shift_id' => $booking->getShiftId(),
            'date' => $booking->getDate()->format(DATE_ATOM),
            'overseer_id' => $booking->getOverseerId(),
            'pioneer_id' => $booking->getPioneerId(),
            'pioneer_b_id' => $booking->getPioneerBId(),
            'confirmed' => $booking->isConfirmed() ? 'y' : 'n',
            'full' => $booking->isFull() ? 'y' : 'n',
            'recorded' => $booking->isRecorded() ? 'y' : 'n',
            'placements' => $booking->getPlacements(),
            'videos' => $booking->getVideos(),
            'requests' => $booking->getRequests(),
            'comments' => $booking->getComments(),
            'experience' => $booking->isExperience() ? 'y' : 'n',
        ];
    }

}
