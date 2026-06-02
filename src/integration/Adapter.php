<?php
/**
 * Abstract base class for plugin integration adapters.
 *
 * Provides a blueprint for integrating with external plugins or modules
 * (e.g., Contact Form 7, WooCommerce).
 *
 * Subclasses must implement `registerHooks()` to attach actions and filters
 * provided by the integrated plugin.
 *
 * @since 0.5.0
 */

namespace BitskiWPCF7Booking\integration;

abstract class Adapter
{
    /**
     * Initializes the integration adapter.
     *
     * Calls `registerHooks()` to attach actions and filters from the external plugin.
     * Should be invoked after instantiating the adapter to ensure hooks are registered.
     */
    public function init(): void
    {
        $this->registerHooks();
    }

    /**
     * Registers hooks for the external integration.
     *
     * Must be implemented by subclasses to attach actions or filters
     * provided by the integrated plugin or module.
     */
    abstract protected function registerHooks(): void;

    /**
     * Logs a debug message to the PHP error log when WP_DEBUG is enabled.
     */
    protected function log(string $message): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Adapter: ' . $message);
        }
    }
}
