<?php
/**
 * Capacity manager.
 *
 * @since 0.4.1
 */

namespace BitskiWPCF7Booking\domain;

use DateInterval;
use DateMalformedStringException;

use BitskiWPCF7Booking\infrastructure\ReservationRepository;

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
    public function isCapacityAvailable(Reservation $reservation): bool
    {
        $overlappingReservations = $this->findOverlappingReservations($reservation);

        $guestCountEvents = $this->createGuestCountEvents($overlappingReservations);

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
    private function findOverlappingReservations(Reservation $reservation): array
    {
        $startAt         = $reservation->getStartAt();
        $durationMinutes = $reservation->getDurationMinutes();
        $endAt           = $startAt->add(
            new DateInterval("PT{$durationMinutes}M")
        );

        return $this->reservationRepository->findOverlappingReservations($startAt, $endAt);
    }

    /**
     *
     */
    private function createGuestCountEvents(array $overlappingReservations): array
    {
        foreach ($overlappingReservations as $overlappingReservation) {
            // TODO: Create guest count event.
        }

        return [];
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
