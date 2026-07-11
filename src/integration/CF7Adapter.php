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
use InvalidArgumentException;
use WPCF7_Submission;

use BitskiWPCF7Booking\application\BookingService;
use BitskiWPCF7Booking\domain\Reservation;

class CF7Adapter extends Adapter
{
    private BookingService $bookingService;

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
     * When the booking is not accepted:
     * - Sets the $abort reference parameter to true, which prevents CF7 from sending the email.
     * - Sets the appropriate response message in the submission object.
     *
     * @since 0.1.2
     */
    public function handleBookingRequest($contactForm, bool &$abort, WPCF7_Submission $submission): void
    {
        $payload     = $submission->get_posted_data();
        $reservation = $this->mapToReservation($payload);

        if ($this->bookingService->book($reservation) === false) {
            $abort = true;

            $responseMessage = sprintf(
                'Leider sind für den gewünschten Termin am %s um %s Uhr für %d %s nicht mehr ausreichend viele Plätze in unserem Restaurant verfügbar. Bitte wählen Sie eine andere Uhrzeit oder ein anderes Datum.',
                $reservation->getStartAt()->format('d.m.Y'),
                $reservation->getStartAt()->format('H:i'),
                $reservation->getGuestCount(),
                $reservation->getGuestCount() > 1 ? 'Personen' : 'Person'
            );
            $submission->set_response($responseMessage);
        }
    }

    /**
     * Maps CF7 payload to a Reservation object.
     */
    public function mapToReservation(array $payload): Reservation
    {
        $name  = $payload['your-name'] ?? '';
        $phone = $payload['your-telefon'] ?? '';
        $email = $payload['your-email'] ?? '';
        $date  = $payload['your-datum'] ?? '';
        $time  = $payload['your-zeit'] ?? '';
        try {
            $startAt = new DateTimeImmutable($date . ' ' . $time);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw new InvalidArgumentException('Invalid date format.', 0, $e);
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
