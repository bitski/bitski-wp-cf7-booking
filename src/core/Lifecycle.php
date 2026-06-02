<?php
/**
 * Plugin lifecycle class.
 *
 * Registers plugin lifecycle hooks (activation, deactivation, uninstall).
 *
 * @since 0.1.4
 */

namespace BitskiWPCF7Booking\core;

class Lifecycle
{
    /**
     * Initializes lifecycle class.
     */
    public function init(): void
    {
        register_activation_hook(BITSKI_WP_CF7_BOOKING_FILE, [$this, 'activate']);
        register_deactivation_hook(BITSKI_WP_CF7_BOOKING_FILE, [$this, 'deactivate']);
        register_uninstall_hook(BITSKI_WP_CF7_BOOKING_FILE, [self::class, 'uninstall']
        ); // Static callback required for this WordPress hook.
    }

    /**
     * Plugin activation logic.
     */
    public function activate(): void
    {
        error_log('Plugin activated');
    }

    /**
     * Plugin deactivation logic.
     */
    public function deactivate(): void
    {
        error_log('Plugin deactivated');
    }

    /**
     * Plugin uninstallation logic.
     * The callback of the uninstall hook must be static.
     */
    public static function uninstall(): void
    {
        error_log('Plugin uninstalled');

        // Deletes all plugin options registered in the table 'wp_options'.
        $pluginOptionsToDelete = [
            BITSKI_WP_CF7_BOOKING_SLUG.'_options',
            // Add other options to be deleted here.
        ];
        foreach ($pluginOptionsToDelete as $option) {
            delete_option($option);
        }

        // Deletes all plugin transients registered in the table 'wp_options'.
        $pluginTransientsToDelete = [
            BITSKI_WP_CF7_BOOKING_SLUG.'_admin_notices',
            // Add other transients to be deleted here.
        ];
        foreach ($pluginTransientsToDelete as $transient) {
            delete_transient($transient);
        }

        // Add other plugin-specific cleanup logic here.
        // For example, deleting all posts of a custom post type the plugin creates.
    }
}
