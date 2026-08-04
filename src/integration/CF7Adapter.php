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
        add_action(
            'wpcf7_before_send_mail',
            [$this, 'handleBookingRequest'],
            10,
            3,
        );
    }

    /**
     * Handles the booking request before sending the email.
     *
     * Workflow:
     * - Validates incoming CF7 payload.
     * - Maps valid payload data to a Reservation.
     * - Delegates booking decision to BookingService.
     *
     * In case of invalid payloads or non-accepted bookings:
     * - Sets the $abort reference parameter to true, which prevents CF7 from sending the email.
     * - Sets the appropriate response message in the submission object.
     *
     * @since 0.1.2
     */
    public function handleBookingRequest(
        $contactForm,
        bool &$abort,
        WPCF7_Submission $submission,
    ): void {
        $payload = $submission->get_posted_data();

        if ( ! $this->validatePayload($payload)) {
            $abort = true;

            $responseMessage
                = 'Leider konnten wir Ihre Reservierungsanfrage nicht verarbeiten. Bitte überprüfen Sie, ob Name, E-Mail-Adresse, Datum, Uhrzeit und Personenzahl korrekt ausgefüllt sind, und versuchen Sie es erneut.';
            $submission->set_response($responseMessage);

            return;
        }

        $payload = $this->sanitizePayload($payload);

        $reservation = $this->mapToReservation($payload);

        if ($this->bookingService->book($reservation) === false) {
            $abort = true;

            $responseMessage = sprintf(
                'Leider sind für den gewünschten Termin am %s um %s Uhr für %d %s nicht mehr ausreichend viele Plätze in unserem Restaurant verfügbar. Bitte wählen Sie eine andere Uhrzeit oder ein anderes Datum.',
                $reservation->getStartAt()->format('d.m.Y'),
                $reservation->getStartAt()->format('H:i'),
                $reservation->getGuestCount(),
                $reservation->getGuestCount() > 1 ? 'Personen' : 'Person',
            );
            $submission->set_response($responseMessage);
        }
    }

    /**
     * Validates CF7 payload data before mapping.
     *
     * Performs boundary validation only.
     * Does not contain booking business rules.
     *
     * @since 0.4.4
     */
    protected function validatePayload(array $payload): bool
    {
        if ( ! isset($payload['your-name'])
            || trim($payload['your-name']) === ''
        ) {
            return false;
        }

        if ( ! isset($payload['your-email'])
            ||
            ! filter_var(
                trim($payload['your-email']),
                FILTER_VALIDATE_EMAIL,
            )
        ) {
            return false;
        }

        if ( ! isset($payload['your-datum'])
            || trim($payload['your-datum']) === ''
        ) {
            return false;
        }

        if (
            ! isset($payload['your-zeit']) || trim($payload['your-zeit']) === ''
        ) {
            return false;
        }

        try {
            new DateTimeImmutable(
                trim($payload['your-datum']) . ' ' . trim(
                    $payload['your-zeit'],
                ),
            );
        } catch (\Exception $e) {
            return false;
        }

        if ( ! isset($payload['your-personenzahl'])) {
            return false;
        }

        $guestCount = trim($payload['your-personenzahl']);

        if (
            $guestCount === ''
            ||
            ! ctype_digit($guestCount)
            || (int)$guestCount < 1
        ) {
            return false;
        }

        return true;
    }

    /**
     * Sanitizes CF7 validated payload data before mapping.
     *
     * @since 0.4.5
     */
    protected function sanitizePayload(array $payload): array
    {
        $payload['your-name'] = sanitize_text_field($payload['your-name']);
        $payload['your-telefon'] = sanitize_text_field($payload['your-telefon'] ?? '');
        $payload['your-email'] = sanitize_email($payload['your-email']);
        $payload['your-datum'] = sanitize_text_field($payload['your-datum']);
        $payload['your-zeit'] = sanitize_text_field($payload['your-zeit']);
        $payload['your-personenzahl'] = sanitize_text_field($payload['your-personenzahl']);
        $payload['your-res-comment'] = sanitize_textarea_field($payload['your-res-comment'] ?? '');

        return $payload;
    }


    /**
     * Maps validated and sanitized CF7 payload data to a Reservation object.
     */
    public function mapToReservation(array $payload): Reservation
    {
        $name = $payload['your-name'];
        $phone = $payload['your-telefon'];
        $email = $payload['your-email'];
        $date = $payload['your-datum'];
        $time = $payload['your-zeit'];
        try {
            $startAt = new DateTimeImmutable($date . ' ' . $time);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw new InvalidArgumentException('Invalid date format.', 0, $e);
        }
        $durationMinutes = 120;
        $guestCount      = (int)$payload['your-personenzahl'];
        $message         = $payload['your-res-comment'];

        return new Reservation(
            name: $name,
            phone: $phone,
            email: $email,
            startAt: $startAt,
            durationMinutes: $durationMinutes,
            guestCount: $guestCount,
            message: $message,
        );
    }
}
