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

use BitskiWPCF7Booking\application\BookingService;

class CF7Adapter extends Adapter
{
    protected BookingService $bookingService;

    /**
     * Main class name of the external plugin or module required by this adapter.
     */
    protected string $dependencyClass = 'WPCF7';

    /**
     * Injects dependencies for the CF7Adapter.
     *
     * @param  BookingService  $bookingService
     *
     * @since 0.1.2
     */
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
        error_log('CF7Adapter constructor called');
        error_log('BookingService injected: ' . ($bookingService instanceof BookingService ? 'true' : 'false'));
    }

    /**
     * Registers hooks for the external integration.
     */
    protected function registerHooks(): void
    {
        add_action('wpcf7_before_send_mail', [$this, 'handleBookingRequest']);
        add_filter('wpcf7_posted_data', [$this, 'captureFormPayload']);
    }

    /**
     * @since 0.1.2
     */
    public function handleBookingRequest()
    {
        error_log('Booking request received');
    }

    /**
     * @since 0.1.2
     */
    public function captureFormPayload() {

    }
}
