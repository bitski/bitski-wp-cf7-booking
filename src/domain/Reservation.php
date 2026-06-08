<?php
/**
 * Reservation (Domain Entity).
 *
 * Represents a confirmed booking in the domain.
 * Contains all necessary data to evaluate capacity and persist the reservation.
 * Contains no business logic.
 *
 * @since 0.2.0
 */

namespace BitskiWPCF7Booking\domain;

class Reservation
{
    protected string $name;
    protected string $phone;
    protected string $email;
    protected \DateTimeImmutable $startAt;

    /**
     * Duration of the reservation in minutes.
     */
    protected int $durationMinutes = 120;

    protected int $guestCount;
    protected string $message;
}
