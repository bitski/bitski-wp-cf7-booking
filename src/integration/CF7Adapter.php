<?php
/**
 * CF7Adapter.
 *
 * Adapter for integrating with CF7 plugin.
 * Depends on BookingService injected via the constructor.
 *
 * @since 0.1.0
 */

namespace BitskiWPCF7Booking\integration;

use DateTimeImmutable;
use http\Exception\InvalidArgumentException;
use WPCF7_Submission;

use BitskiWPCF7Booking\application\BookingService;
use BitskiWPCF7Booking\domain\Reservation;

class CF7Adapter extends Adapter
{
    protected BookingService $bookingService;

    /**
     * Main class name of the external plugin or module required by this adapter.
     */
    protected string $dependencyClass = 'WPCF7';

    /**
     * @param  BookingService  $bookingService
     *
     * @since 0.1.2
     */
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
        error_log('CF7Adapter constructor called');
    }

    /**
     * Registers hooks for the external integration.
     */
    protected function registerHooks(): void
    {
        add_action('wpcf7_before_send_mail', [$this, 'handleBookingRequest'], 10, 3);
    }

    /**
     * Handles the booking request before sending the email.
     *
     * @since 0.1.2
     */
    public function handleBookingRequest($contactForm, bool &$abort, WPCF7_Submission $submission)
    {
        error_log('Booking request received');
        /**
         * Retrieves the form submission data from the WPCF7_Submission singleton.
         * Maps the CF7 form payload to a Reservation object and passes it to the BookingService.
         */
        $payload     = $submission->get_posted_data();
        $reservation = $this->mapToReservation($payload);

        return $this->bookingService->book($reservation);
    }

    /**
     * Maps the CF7 form payload to a Reservation domain object.
     * Ensures the Reservation object is correctly populated with form data.
     */
    public function mapToReservation(array $payload): Reservation
    {
        /**
         * Maps the CF7 form payload data to Reservation domain object properties.
         */
        $name  = $payload['your-name'] ?? '';
        $phone = $payload['your-telefon'] ?? '';
        $email = $payload['your-email'] ?? '';
        try {
            $startAt = new DateTimeImmutable($payload['your-datum']);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw new InvalidArgumentException('Invalid date format.');
        }
        $durationMinutes = 120;
        $guestCount      = isset($payload['your-personenzahl']) ? (int)$payload['your-personenzahl'] : 1;
        $message         = $payload['your-res-comment'] ?? '';

        /**
         * CF7 boundary safety: enforces minimal valid guest count.
         * No business rule, input sanitization only.
         */
        if ($guestCount <= 0) {
            $guestCount = 1;
        }

        /**
         * Creates a new Reservation object with the mapped data.
         */
        return new Reservation(
            name: $name,
            phone: $phone,
            email: $email,
            startAt: $startAt,
            durationMinutes: $durationMinutes,
            guestCount: $guestCount,
            message: $message
        );
    }
}
