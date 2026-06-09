<?php
/**
 * Booking service.
 *
 * @since 0.2.1
 */

namespace BitskiWPCF7Booking\application;

use BitskiWPCF7Booking\domain\Reservation;

class BookingService
{
    public function __construct()
    {
        // TODO: Phase 4:
        // $this->capacityManager = new CapacityManager();
        // Add dependencies here (Repository).
    }

    public function book(Reservation $reservation): bool
    {
        if ( ! $this->accept($reservation)) {
            return false;
        }

        // TODO: Phase 4: ReservationRepository->save($reservation);

        return true;
    }

    public function accept(Reservation $reservation): bool
    {
        // TODO: Phase 4:
        // if (!capacityManager->isCapacityAvailable($reservation) {
        //     return false;
        // }

        return true;
    }
}
