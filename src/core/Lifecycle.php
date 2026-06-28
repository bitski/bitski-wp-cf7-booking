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

        $this->createReservationsTable();
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
            BITSKI_WP_CF7_BOOKING_SLUG . '_options',
            // Add other options to be deleted here.
        ];
        foreach ($pluginOptionsToDelete as $option) {
            delete_option($option);
        }

        // Deletes all plugin transients registered in the table 'wp_options'.
        $pluginTransientsToDelete = [
            BITSKI_WP_CF7_BOOKING_SLUG . '_admin_notices',
            // Add other transients to be deleted here.
        ];
        foreach ($pluginTransientsToDelete as $transient) {
            delete_transient($transient);
        }

        self::dropReservationsTable();

        // Add other plugin-specific cleanup logic here.
        // For example, deleting all posts of a custom post type the plugin creates.
    }

    /**
     * Creates the reservations table if it doesn't exist.
     *
     * @since 0.3.1
     */
    protected function createReservationsTable(): void
    {
        global $wpdb;

        $tableName = $wpdb->prefix . BITSKI_WP_CF7_BOOKING_TABLE_RESERVATIONS;

        $charsetCollate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $tableName (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            email VARCHAR(255) NOT NULL,
            start_at DATETIME NOT NULL,
            duration_minutes INT NOT NULL,
            guest_count INT NOT NULL,
            message TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            
            PRIMARY KEY (id),
            KEY start_at (start_at)           
        ) $charsetCollate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Drops the reservations table if it exists.
     *
     * @since 0.3.1
     */
    protected static function dropReservationsTable(): void
    {
        global $wpdb;

        $tableName = $wpdb->prefix . BITSKI_WP_CF7_BOOKING_TABLE_RESERVATIONS;

        $sql = "DROP TABLE IF EXISTS $tableName;";

        $wpdb->query($sql);
    }
}
