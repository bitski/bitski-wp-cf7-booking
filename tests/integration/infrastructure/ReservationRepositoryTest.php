<?php

declare(strict_types=1);

namespace BitskiWPCF7Booking\Tests\integration\infrastructure;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

use BitskiWPCF7Booking\infrastructure\ReservationRepository;
use BitskiWPCF7Booking\domain\Reservation;

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

    public function testFindOverlappingReservationsReturnsOverlappingReservations(): void {
        $existingReservation = new Reservation(
            'John Doe',
            '123-456-7890',
            'john@example.com',
            new DateTimeImmutable('2026-08-01 18:00:00'),
            120,
            4,
            'message content'
        );

        $saved = $this->reservationRepository->save($existingReservation);
        $this->assertTrue($saved, 'Reservation could not be saved');

        $startAt = new DateTimeImmutable('2026-08-01 18:30:00');
        $endAt = new DateTimeImmutable('2026-08-01 20:30:00');

        $overlappingReservations = $this->reservationRepository->findOverlappingReservations($startAt, $endAt);
        $this->assertCount(1, $overlappingReservations, 'Expected exactly 1 overlapping reservation' . print_r($overlappingReservations, true));
        $this->assertSame('john@example.com', $overlappingReservations[0]->email, 'Expected email to match');
    }

    public function testFindOverlappingReservationsReturnsEmptyResultWhenNoOverlap(): void {
        $existingReservation = new Reservation(
            'John Doe',
            '123-456-7890',
            'john@example.com',
            new DateTimeImmutable('2026-08-01 18:00:00'),
            120,
            4,
            'message content'
        );

        $saved = $this->reservationRepository->save($existingReservation);

        $startAt = new DateTimeImmutable('2027-08-01 18:30:00');
        $endAt = new DateTimeImmutable('2027-08-01 20:30:00');

        $overlappingReservations = $this->reservationRepository->findOverlappingReservations($startAt, $endAt);
        $this->assertEmpty($overlappingReservations, 'Expected return of empty array if there is no overlapping reservation');
    }
}
