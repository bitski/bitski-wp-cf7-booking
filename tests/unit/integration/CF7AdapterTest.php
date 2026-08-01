<?php

declare(strict_types=1);

namespace BitskiWPCF7Booking\Tests\unit\integration;

use BitskiWPCF7Booking\application\BookingService;
use PHPUnit\Framework\TestCase;

use BitskiWPCF7Booking\integration\CF7Adapter;
use BitskiWPCF7Booking\domain\Reservation;

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
}
