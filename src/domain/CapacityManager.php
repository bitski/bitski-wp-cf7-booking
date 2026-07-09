<?php
/**
 * Capacity manager.
 *
 * @since 0.4.1
 */

namespace BitskiWPCF7Booking\domain;

use DateInterval;
use DateTimeImmutable;
use DateMalformedIntervalStringException;
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
     * Checks if the reservation is within the capacity limits.
     */
    public function isCapacityAvailable(Reservation $reservation, DateTimeImmutable $endAt): bool
    {
        $overlappingReservations = $this->findOverlappingReservations($reservation, $endAt);

        $guestCountEvents = $this->createGuestCountEvents($reservation, $endAt, $overlappingReservations);

        $highestGuestCount = $this->calculateHighestGuestCount($reservation, $endAt, $guestCountEvents);

        if ($highestGuestCount > 20) {
            return false;
        }

        return true;
    }

    /**
     * Returns overlapping reservations.
     *
     * @throws DateMalformedStringException
     */
    private function findOverlappingReservations(Reservation $reservation, DateTimeImmutable $endAt): array
    {
        $startAt = $reservation->getStartAt();

        return $this->reservationRepository->findOverlappingReservations($startAt, $endAt);
    }

    /**
     * Returns guest count events sorted chronologically.
     *
     * Each event is an associative array with the following keys:
     * - eventAt: The timestamp of the event.
     * - guestCountChange: The change in guest count (positive for starting reservations, negative for ending reservations).
     *
     * @throws DateMalformedIntervalStringException
     */
    private function createGuestCountEvents(
        Reservation $reservation,
        DateTimeImmutable $endAt,
        array $overlappingReservations
    ): array {
        $guestCountEvents = [];

        $guestCountEvents[] = [
            'eventAt'          => $reservation->getStartAt(),
            'guestCountChange' => $reservation->getGuestCount()
        ];
        $guestCountEvents[] = ['eventAt' => $endAt, 'guestCountChange' => -$reservation->getGuestCount()];

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
            $result = $a['eventAt'] <=> $b['eventAt'];

            if ($result !== 0) {
                return $result;
            }

            // Sorts departures before arrivals in case of equal timestamps.
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
     * Returns the highest guest count during the reservation interval.
     */
    private function calculateHighestGuestCount(
        Reservation $reservation,
        DateTimeImmutable $endAt,
        array $guestCountEvents
    ): int {
        $currentGuestCount = 0;

        foreach ($guestCountEvents as $key => $event) {
            $currentGuestCount                    += $event['guestCountChange'];
            $guestCountEvents[$key]['guestCount'] = $currentGuestCount;
        }

        $intervalGuestCountEvents = array_filter(
            $guestCountEvents,
            static function ($event) use ($reservation, $endAt) {
                return ($event['eventAt'] >= $reservation->getStartAt() && $event['eventAt'] <= $endAt);
            }
        );

        return max(array_column($intervalGuestCountEvents, 'guestCount'));
    }
}
