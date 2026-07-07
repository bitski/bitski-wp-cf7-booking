<?php
/**
 * Booking service.
 *
 * @since 0.2.1
 */

namespace BitskiWPCF7Booking\application;

use BitskiWPCF7Booking\domain\CapacityManager;
use BitskiWPCF7Booking\domain\Reservation;
use BitskiWPCF7Booking\infrastructure\ReservationRepository;

class BookingService
{
    private ReservationRepository $reservationRepository;
    private CapacityManager $capacityManager;

    public function __construct(
        ReservationRepository $reservationRepository,
        CapacityManager $capacityManager
    )
    {
        error_log('BookingService constructed with dependencies: '. get_class($reservationRepository));

        $this->reservationRepository = $reservationRepository;
        $this->capacityManager = $capacityManager;
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
         if (!$this->capacityManager->isCapacityAvailable($reservation)) {
             return false;
         }

        return true;
    }
}
