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

use DateTimeImmutable;

class Reservation
{
    protected string $name;
    protected string $phone;
    protected string $email;
    protected DateTimeImmutable $startAt;

    /**
     * Duration of the reservation in minutes.
     */
    protected int $durationMinutes;

    protected int $guestCount;
    protected string $message;

    public function __construct(
        string $name,
        string $phone,
        string $email,
        DateTimeImmutable $startAt,
        int $durationMinutes,
        int $guestCount,
        string $message
    ) {
        $this->name            = $name;
        $this->phone           = $phone;
        $this->email           = $email;
        $this->startAt         = $startAt;
        $this->durationMinutes = $durationMinutes;
        $this->guestCount      = $guestCount;
        $this->message         = $message;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getStartAt(): DateTimeImmutable
    {
        return $this->startAt;
    }

    public function getDurationMinutes(): int
    {
        return $this->durationMinutes;
    }

    public function getGuestCount(): int
    {
        return $this->guestCount;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
