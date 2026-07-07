<?php
/**
 * Capacity manager.
 *
 * @since 0.4.1
 */

namespace BitskiWPCF7Booking\domain;

use DateInterval;
use DateMalformedIntervalStringException;
use DateMalformedStringException;

use BitskiWPCF7Booking\infrastructure\ReservationRepository;
use DateTimeImmutable;

class CapacityManager
{
    private ReservationRepository $reservationRepository;

    public function __construct(ReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    /**
     *
     */
    public function isCapacityAvailable(Reservation $reservation, DateTimeImmutable $endAt): bool
    {
        $overlappingReservations = $this->findOverlappingReservations($reservation, $endAt);

        $guestCountEvents = $this->createGuestCountEvents($overlappingReservations, $reservation, $endAt);

        $highestGuestCount = $this->calculateHighestGuestCount($guestCountEvents);

        if ($highestGuestCount > 20) {
            return false;
        }

        return true;
    }

    /**
     * Returns an array of overlapping reservations.
     *
     * @throws DateMalformedStringException
     */
    private function findOverlappingReservations(Reservation $reservation, DateTimeImmutable $endAt): array
    {
        $startAt = $reservation->getStartAt();

        return $this->reservationRepository->findOverlappingReservations($startAt, $endAt);
    }

    /**
     *
     * @throws DateMalformedIntervalStringException
     */
    private function createGuestCountEvents(
        array $overlappingReservations,
        Reservation $reservation,
        DateTimeImmutable $endAt
    ): array {
        $startAt          = $reservation->getStartAt();
        $guestCountEvents = [];

        foreach ($overlappingReservations as $overlappingReservation) {
            $overlapStartAt   = $overlappingReservation->getStartAt();
            $overlapEndAt     = $overlapStartAt->add(
                new DateInterval('PT' . $overlappingReservation->getDurationMinutes() . 'M')
            );
            $guestCountChange = $overlappingReservation->getGuestCount();

            $guestCountEvents[] = ['eventAt' => $overlapStartAt, 'guestCountChange' => $guestCountChange];
            $guestCountEvents[] = ['eventAt' => $overlapEndAt, 'guestCountChange' => -$guestCountChange];
        }

        /**
         * Sorts guest count events chronologically.
         *
         * Events with the same timestamp are ordered by guest count change:
         * ending reservations (-) are processed before starting reservations (+).
         */
        usort($guestCountEvents, static function ($a, $b) {
            if ($a['eventAt'] < $b['eventAt']) {
                return -1;
            }

            if ($a['eventAt'] > $b['eventAt']) {
                return 1;
            }

            if ($a['guestCountChange'] < 0 && $b['guestCountChange'] > 0) {
                return -1;
            }

            if ($a['guestCountChange'] > 0 && $b['guestCountChange'] < 0) {
                return 1;
            }

            return 0;
        });

        return $guestCountEvents;
    }

    /**
     *
     */
    private function calculateHighestGuestCount(array $guestCountEvents): int
    {
        // TODO: Calculate highest guest count.

        return 0;
    }
}
