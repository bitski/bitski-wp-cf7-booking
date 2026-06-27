<?php

/**
 * Reservation repository.
 *
 * @since 0.3.0
 */

namespace BitskiWPCF7Booking\infrastructure;

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
}
