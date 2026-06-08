<?php

namespace BitskiWPCF7Booking\application;

use BitskiWPCF7Booking\domain\Reservation;

class BookingService
{
    public function __construct() {
        // TODO: Phase 4:
        // $this->capacityManager = new CapacityManager();
        // Add dependencies here (Repository).
    }

    public function accepts(Reservation $reservation):bool
    {
        // TODO: Phase 4:
        // if (!capacityManager->isCapacityAvailable($reservation) {
        //     return false;
        // }

        return true;
    }
}
