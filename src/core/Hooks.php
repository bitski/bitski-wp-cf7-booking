<?php
/**
 * Core plugin hooks manager.
 *
 * Handles all hooks that are triggered and consumed within the plugin itself.
 * Provides a centralized place to register base and functional hooks for
 * internal coordination or module interaction.
 *
 * @since 0.1.3
 */

namespace BitskiWPCF7Booking\core;

class Hooks
{
    /**
     * Initializes internal plugin hooks.
     *
     * Sets up base hooks (essential plugin-level events) and
     * functional hooks (feature-specific plugin events, e.g., archive handling, admin actions).
     *
     * These hooks are intended only for internal plugin coordination.
     */
    public function init(): void
    {
        $this->registerBaseHooks();         // Base hooks, e.g.: image sizes.
        $this->registerFunctionalHooks();   // Functional hooks, e.g., JS events, archive query, footer actions.
    }

    /**
     * Registers base hooks for essential plugin functionality.
     */
    protected function registerBaseHooks(): void
    {
    }

    /**
     * Registers functional hooks for feature-specific plugin behavior.
     */
    protected function registerFunctionalHooks(): void
    {
        // Archive hooks.
        // (To be inhabited)

        // Header hooks.
        // (To be inhabited)

        // Page hooks.
        // (To be inhabited)

        // Footer hooks.
        // (To be inhabited)
    }
}
