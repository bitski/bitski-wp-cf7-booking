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

    public function testFindOverlappingReservationsReturnsSingleOverlap(): void
    {
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
        $endAt   = new DateTimeImmutable('2026-08-01 20:30:00');

        $overlappingReservations = $this->reservationRepository->findOverlappingReservations($startAt, $endAt);
        $this->assertCount(
            1,
            $overlappingReservations,
            'Expected exactly 1 overlapping reservation' . print_r($overlappingReservations, true)
        );
        $this->assertSame('john@example.com', $overlappingReservations[0]->getEmail(), 'Expected email to match');
    }

    public function testFindOverlappingReservationsReturnsNoOverlap(): void
    {
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
        $endAt   = new DateTimeImmutable('2027-08-01 20:30:00');

        $overlappingReservations = $this->reservationRepository->findOverlappingReservations($startAt, $endAt);

        $this->assertEmpty(
            $overlappingReservations,
            'Expected return of empty array if there is no overlapping reservation'
        );
    }

    public function testFindOverlappingReservationsReturnsMultipleOverlaps(): void
    {
        $existingReservationsData = [
            [
                'John Doe',
                '111-111-111',
                'john@example.com',
                new DateTimeImmutable('2026-08-01 18:00:00'),
                120,
                4,
                'Reservation 1',
            ],
            [
                'Jane Doe',
                '222-222-222',
                'jane@example.com',
                new DateTimeImmutable('2026-08-01 20:30:00'),
                120,
                2,
                'Reservation 2',
            ],
            [
                'Max Mustermann',
                '333-333-333',
                'max@example.com',
                new DateTimeImmutable('2026-08-02 12:00:00'),
                120,
                3,
                'Reservation 3',
            ],
            [
                'Erika Musterfrau',
                '444-444-444',
                'erika@example.com',
                new DateTimeImmutable('2026-08-02 18:00:00'),
                120,
                5,
                'Reservation 4',
            ],
            [
                'Peter Parker',
                '555-555-555',
                'peter@example.com',
                new DateTimeImmutable('2026-08-03 19:00:00'),
                120,
                2,
                'Reservation 5',
            ],
            [
                'Bruce Wayne',
                '666-666-666',
                'bruce@example.com',
                new DateTimeImmutable('2026-08-04 17:00:00'),
                120,
                6,
                'Reservation 6',
            ],
        ];

        foreach ($existingReservationsData as $data) {
            $existingReservation = new Reservation(...$data);
            $saved               = $this->reservationRepository->save($existingReservation);
            $this->assertTrue($saved, 'Reservation could not be saved');
        }

        $startAt = new DateTimeImmutable('2026-08-01 18:30:00');
        $endAt   = new DateTimeImmutable('2026-08-01 20:30:00');

        $overlappingReservations = $this->reservationRepository->findOverlappingReservations($startAt, $endAt);
        $emails                  = array_map(static function ($overlappingReservation) {
            return $overlappingReservation->getEmail();
        }, $overlappingReservations);
        $this->assertCount(
            2,
            $overlappingReservations,
            'Expected exactly 2 overlapping reservations:' . print_r($overlappingReservations, true)
        );
        $this->assertEqualsCanonicalizing(['john@example.com', 'jane@example.com'],
            $emails,
            'Expected emails to match');
    }
}
