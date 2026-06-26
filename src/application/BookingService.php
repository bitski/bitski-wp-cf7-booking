<?php
/**
 * Booking service.
 *
 * @since 0.2.1
 */

namespace BitskiWPCF7Booking\application;

use BitskiWPCF7Booking\domain\Reservation;
use BitskiWPCF7Booking\infrastructure\ReservationRepository;

class BookingService
{
    private ReservationRepository $reservationRepository;

    public function __construct(ReservationRepository $reservationRepository)
    {
        // TODO: Phase 4:
        // $this->capacityManager = new CapacityManager();
        // Add dependencies here (Repository).

        error_log('BookingService constructed with dependencies: '. get_class($reservationRepository));

        $this->reservationRepository = $reservationRepository;
    }

    public function book(Reservation $reservation): bool
    {
        if ( ! $this->accept($reservation)) {
            return false;
        }

        return $this->reservationRepository->save($reservation);
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
