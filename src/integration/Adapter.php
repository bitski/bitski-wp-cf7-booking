<?php
/**
 * Abstract base class for plugin integration adapters.
 *
 * Provides a blueprint for integrating with external plugins or modules
 * (e.g., Contact Form 7, WooCommerce).
 *
 * Subclasses must implement `registerHooks()` and typically set the appropriate `$dependencyClass`
 * to attach actions and filters provided by the integrated plugin.
 *
 * @since 0.5.0
 */

namespace BitskiWPCF7Booking\integration;

abstract class Adapter
{
    /**
     * Main class name of the external plugin or module required by this adapter.
     *
     * @since 1.0.1
     */
    protected string $dependencyClass = '';

    /**
     * Initializes the integration adapter.
     *
     * Checks whether the external plugin is available and, if so, registers its hooks.
     * If the dependency is not available, logs a debug message and returns early,
     * skipping the rest of the initialization.
     */
    public function init(): void
    {
        if (!$this->isAvailable()) {
            $this->log($this->dependencyClass . ' is not available.');
            return;
        }

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
     * Checks if the external plugin is available.
     *
     * By default, this checks whether the configured dependency class exists.
     * Subclasses may override this method for custom availability checks.
     *
     * @since 1.0.1
     */
    protected function isAvailable(): bool
    {
        return '' !== $this->dependencyClass && class_exists($this->dependencyClass);
    }

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
