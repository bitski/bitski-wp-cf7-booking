<?php
/**
 * Reservation repository.
 *
 * @since 0.3.0
 */

namespace BitskiWPCF7Booking\infrastructure;

use DateMalformedStringException;
use DateTimeImmutable;

use BitskiWPCF7Booking\domain\Reservation;

class ReservationRepository
{
    /**
     * Saves reservation to the database.
     * Returns true if the reservation was saved successfully, false otherwise.
     *
     * @since 0.3.3
     */
    public function save(Reservation $reservation): bool
    {
        global $wpdb;

        $tableName = $wpdb->prefix . BITSKI_WP_CF7_BOOKING_TABLE_RESERVATIONS;

        $data = [
            'name'             => $reservation->getName(),
            'phone'            => $reservation->getPhone(),
            'email'            => $reservation->getEmail(),
            'start_at'         => $reservation->getStartAt()->format('Y-m-d H:i:s'),
            'duration_minutes' => $reservation->getDurationMinutes(),
            'guest_count'      => $reservation->getGuestCount(),
            'message'          => $reservation->getMessage(),
        ];

        $result = $wpdb->insert($tableName, $data);

        return (bool)$result;
    }

    /**
     * Finds overlapping reservations in the database.
     * Returns an array of reservation objects.
     *
     * @throws DateMalformedStringException
     * @since 0.3.4
     */
    public function findOverlappingReservations(DateTimeImmutable $startAt, DateTimeImmutable $endAt): array
    {
        global $wpdb;

        $tableName = $wpdb->prefix . BITSKI_WP_CF7_BOOKING_TABLE_RESERVATIONS;

        $sql = $wpdb->prepare(
            "SELECT * FROM {$tableName} WHERE
            start_at <= %s
            AND DATE_ADD(start_at, INTERVAL duration_minutes MINUTE) >= %s
        ",
            $endAt->format('Y-m-d H:i:s'),
            $startAt->format('Y-m-d H:i:s')
        );

        $result = $wpdb->get_results($sql);

        $overlappingReservations = [];

        foreach ($result as $reservation) {
            $overlappingReservations[] = new Reservation(
                name: $reservation->name,
                phone: $reservation->phone,
                email: $reservation->email,
                startAt: new DateTimeImmutable($reservation->start_at),
                durationMinutes: $reservation->duration_minutes,
                guestCount: $reservation->guest_count,
                message: $reservation->message
            );
        }

        return $overlappingReservations;
    }
}
