<?php
/**
 * bitski-wp-cf7-booking
 *
 * Slim WordPress plugin integrating PHP OOP principles.
 *
 * @since 0.1.0
 *
 * @wordpress-plugin
 * Plugin Name: bitski-wp-cf7-booking
 * Plugin URI: https://github.com/bitski/bitski-wp-cf7-booking
 * Author: Peter Eckerle
 * Author URI: https://bitski.de
 * Description: Slim WordPress plugin integrating PHP OOP principles.
 * Version: 0.3.2
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * License: GNU General Public License v3.0 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: bitski-wp-cf7-booking
 * Domain Path: /languages
 */

// Defines core plugin constants for plugin paths, URLs and identifiers.
define('BITSKI_WP_CF7_BOOKING_FILE', __FILE__);
define('BITSKI_WP_CF7_BOOKING_PATH', plugin_dir_path(BITSKI_WP_CF7_BOOKING_FILE));
define('BITSKI_WP_CF7_BOOKING_URL', plugin_dir_url(BITSKI_WP_CF7_BOOKING_FILE));
define('BITSKI_WP_CF7_BOOKING_SLUG', 'bitski-wp-cf7-booking');
define('BITSKI_WP_CF7_BOOKING_VERSION', '0.3.2');
define('BITSKI_WP_CF7_BOOKING_TABLE_RESERVATIONS', 'bitski_reservations');

// Loads the plugin bootstrap file.
if (file_exists(__DIR__ . '/bootstrap.php')) {
    require_once __DIR__ . '/bootstrap.php';
}
