<?php

declare(strict_types=1);

namespace BitskiWPCF7Booking\Tests\unit\fakes;

use BitskiWPCF7Booking\domain\Reservation;
use BitskiWPCF7Booking\infrastructure\ReservationRepository;

class FakeReservationRepository extends ReservationRepository
{
    public function save(Reservation $reservation): bool
    {
        return true;
    }
}
