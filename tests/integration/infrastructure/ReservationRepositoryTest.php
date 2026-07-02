<?php

declare(strict_types=1);

namespace BitskiWPCF7Booking\tests\integration\infrastructure;

use PHPUnit\Framework\TestCase;

use BitskiWPCF7Booking\infrastructure\ReservationRepository;

class ReservationRepositoryTest extends TestCase
{
    private ReservationRepository $reservationRepository;

    protected function setUp(): void
    {
        global $wpdb;

        $this->reservationRepository = new ReservationRepository();

        $tableName = $wpdb->prefix . BITSKI_WP_CF7_BOOKING_TABLE_RESERVATIONS;
        $wpdb->query("DELETE FROM $tableName");
    }

    protected function tearDown(): void
    {
        global $wpdb;

        $tableName = $wpdb->prefix . BITSKI_WP_CF7_BOOKING_TABLE_RESERVATIONS;
        $wpdb->query("DELETE FROM $tableName");
    }
}
