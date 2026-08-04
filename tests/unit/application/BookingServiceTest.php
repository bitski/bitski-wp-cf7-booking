<?php

declare(strict_types=1);

namespace BitskiWPCF7Booking\Tests\unit\application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

use BitskiWPCF7Booking\application\BookingService;
use BitskiWPCF7Booking\domain\CapacityManager;
use BitskiWPCF7Booking\domain\Reservation;
use BitskiWPCF7Booking\infrastructure\ReservationRepository;

#

use BitskiWPCF7Booking\Tests\unit\fakes\FakeReservationRepository;

class BookingServiceTest extends TestCase
{
    public function testBookReturnsTrueIfCapacityIsAvailable(): void
    {
        $currentReservation = new Reservation(
            'John Doe',
            '123-456-7890',
            'john@example.com',
            new DateTimeImmutable('2026-08-01 18:00:00'),
            120,
            4,
            'message content',
        );

        $mockReservationRepository = $this->createMock(
            ReservationRepository::class,
        );
        $mockReservationRepository->method('save')->willReturn(true);
        $mockCapacityManager = $this->createMock(CapacityManager::class);
        $mockCapacityManager->method('isCapacityAvailable')->willReturn(true);

        $bookingService = new BookingService(
            $mockReservationRepository,
            $mockCapacityManager,
        );
        $bookResult     = $bookingService->book($currentReservation);

        $this->assertTrue(
            $bookResult,
            'Book should return true if capacity is available',
        );
    }

    public function testBookReturnsFalseIfCapacityIsNotAvailable(): void
    {
        $currentReservation = new Reservation(
            'John Doe',
            '123-456-7890',
            'john@example.com',
            new DateTimeImmutable('2026-08-01 18:00:00'),
            120,
            4,
            'message content',
        );

        $mockReservationRepository = $this->createMock(
            ReservationRepository::class,
        );
        $mockReservationRepository->method('save')->willReturn(true);
        $mockCapacityManager = $this->createMock(CapacityManager::class);
        $mockCapacityManager->method('isCapacityAvailable')->willReturn(false);

        $bookingService = new BookingService(
            $mockReservationRepository,
            $mockCapacityManager,
        );
        $bookResult     = $bookingService->book($currentReservation);

        $this->assertFalse(
            $bookResult,
            'Book should return false if capacity is not available',
        );
    }

    public function testBookReturnsTrueWithFakeRepositoryIfCapacityIsAvailable(
    ): void
    {
        $currentReservation = new Reservation(
            'John Doe',
            '123-456-7890',
            'john@example.com',
            new DateTimeImmutable('2026-08-01 18:00:00'),
            120,
            4,
            'message content',
        );

        $fakeReservationRepository = new FakeReservationRepository();

        $mockCapacityManager = $this->createMock(CapacityManager::class);
        $mockCapacityManager->method('isCapacityAvailable')->willReturn(true);

        $bookingService = new BookingService(
            $fakeReservationRepository,
            $mockCapacityManager,
        );
        $bookResult     = $bookingService->book($currentReservation);

        $this->assertTrue(
            $bookResult,
            'Book should return true with fake ReservationRepository if capacity is available',
        );
    }
}
