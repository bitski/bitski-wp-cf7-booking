<?php
/**
 * Plugin setup class.
 *
 * @since 0.1.3
 */

namespace BitskiWPCF7Booking\core;

class Setup
{
    /**
     * Initializes plugin setup
     *
     * Plugin textdomain loading and future setup actions.
     * Additional setup actions/features progressively enhanced here.
     */
    public function init(): void
    {
        add_action('plugins_loaded', [$this, 'loadTextdomain'], 10);
    }

    /**
     * Loads plugin textdomain for translations.
     */
    public function loadTextdomain(): void
    {
        load_plugin_textdomain(
            BITSKI_WP_CF7_BOOKING_SLUG,
            false,
            BITSKI_WP_CF7_BOOKING_PATH . '/languages'
        );
    }
}
