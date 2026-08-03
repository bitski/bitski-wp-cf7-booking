<?php

declare(strict_types=1);

namespace BitskiWPCF7Booking\Tests\unit\integration;

use PHPUnit\Framework\TestCase;

use WPCF7_Submission;

use BitskiWPCF7Booking\application\BookingService;
use BitskiWPCF7Booking\domain\Reservation;
use BitskiWPCF7Booking\integration\CF7Adapter;

class CF7AdapterTest extends TestCase
{
    public function testMapToReservationReturnsReservation()
    {
        $currentPayload = [
            'your-name'         => 'John Doe',
            'your-telefon'      => '123-456-7890',
            'your-email'        => 'john@example.com',
            'your-datum'        => '2026-08-01',
            'your-zeit'         => '18:00',
            'your-personenzahl' => '4',
            'your-res-comment'  => 'message content',
        ];

        $mockBookingService = $this->createMock(BookingService::class);

        $cf7Adapter    = new CF7Adapter($mockBookingService);
        $mappingResult = $cf7Adapter->mapToReservation($currentPayload);

        $this->assertInstanceOf(
            Reservation::class,
            $mappingResult,
            'mapToReservation should return a Reservation object',
        );
    }

    public function testHandleBookingRequestAbortsWhenEmptyName(): void
    {
        $abort          = false;
        $currentPayload = [
            'your-name'         => '',
            'your-telefon'      => '123-456-7890',
            'your-email'        => 'john@example.com',
            'your-datum'        => '2026-08-01',
            'your-zeit'         => '18:00',
            'your-personenzahl' => '4',
            'your-res-comment'  => 'message content',
        ];

        $mockBookingService = $this->createMock(BookingService::class);
        $mockBookingService
            ->expects($this->never())
            ->method('book');

        $submission = $this->createMock(WPCF7_Submission::class);
        $submission
            ->method('get_posted_data')
            ->willReturn($currentPayload);

        $cf7Adapter = new CF7Adapter($mockBookingService);

        $cf7Adapter->handleBookingRequest(
            null,
            $abort,
            $submission,
        );

        $this->assertTrue($abort);
    }

    public function testHandleBookingRequestAbortsWhenInvalidEmail(): void
    {
        $abort          = false;
        $currentPayload = [
            'your-name'         => 'John Doe',
            'your-telefon'      => '123-456-7890',
            'your-email'        => 'john@examplecom',
            'your-datum'        => '2026-08-01',
            'your-zeit'         => '18:00',
            'your-personenzahl' => '4',
            'your-res-comment'  => 'message content',
        ];

        $mockBookingService = $this->createMock(BookingService::class);
        $mockBookingService
            ->expects($this->never())
            ->method('book');

        $submission = $this->createMock(WPCF7_Submission::class);
        $submission
            ->method('get_posted_data')
            ->willReturn($currentPayload);

        $cf7Adapter = new CF7Adapter($mockBookingService);

        $cf7Adapter->handleBookingRequest(
            null,
            $abort,
            $submission,
        );

        $this->assertTrue($abort);
    }

    public function testHandleBookingRequestAbortsWhenInvalidDateTime(): void
    {
        $abort          = false;
        $currentPayload = [
            'your-name'         => 'John Doe',
            'your-telefon'      => '123-456-7890',
            'your-email'        => 'john@example.com',
            'your-datum'        => '202-99-99',
            'your-zeit'         => '18:00',
            'your-personenzahl' => '4',
            'your-res-comment'  => 'message content',
        ];

        $mockBookingService = $this->createMock(BookingService::class);
        $mockBookingService
            ->expects($this->never())
            ->method('book');

        $submission = $this->createMock(WPCF7_Submission::class);
        $submission
            ->method('get_posted_data')
            ->willReturn($currentPayload);

        $cf7Adapter = new CF7Adapter($mockBookingService);

        $cf7Adapter->handleBookingRequest(
            null,
            $abort,
            $submission,
        );

        $this->assertTrue($abort);
    }

    public function testHandleBookingRequestAbortsWhenInvalidGuestCount(): void
    {
        $abort          = false;
        $currentPayload = [
            'your-name'         => 'John Doe',
            'your-telefon'      => '123-456-7890',
            'your-email'        => 'john@example.com',
            'your-datum'        => '2026-08-01',
            'your-zeit'         => '18:00',
            'your-personenzahl' => '-1',
            'your-res-comment'  => 'message content',
        ];

        $mockBookingService = $this->createMock(BookingService::class);
        $mockBookingService
            ->expects($this->never())
            ->method('book');

        $submission = $this->createMock(WPCF7_Submission::class);
        $submission
            ->method('get_posted_data')
            ->willReturn($currentPayload);

        $cf7Adapter = new CF7Adapter($mockBookingService);

        $cf7Adapter->handleBookingRequest(
            null,
            $abort,
            $submission,
        );

        $this->assertTrue($abort);
    }

    public function testHandleBookingRequestDoesNotAbortWhenValidPayload(): void
    {
        $abort          = false;
        $currentPayload = [
            'your-name'         => 'John Doe',
            'your-telefon'      => '123-456-7890',
            'your-email'        => 'john@example.com',
            'your-datum'        => '2026-08-01',
            'your-zeit'         => '18:00',
            'your-personenzahl' => '4',
            'your-res-comment'  => 'message content',
        ];

        $mockBookingService = $this->createMock(BookingService::class);
        $mockBookingService
            ->expects($this->once())
            ->method('book')
            ->willReturn(true);

        $submission = $this->createMock(WPCF7_Submission::class);
        $submission
            ->method('get_posted_data')
            ->willReturn($currentPayload);

        $cf7Adapter = new CF7Adapter($mockBookingService);

        $cf7Adapter->handleBookingRequest(
            null,
            $abort,
            $submission,
        );

        $this->assertFalse($abort);
    }
}
