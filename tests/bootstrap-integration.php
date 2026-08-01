<?php

/**
 * Plugin integration tests bootstrap.
 *
 * Loads the Composer autoloader.
 * Sets WordPress server variables for testing without a web request.
 * Loads WordPress if available and throws an exception if not.
 *
 * @since 0.3.4
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Server variables, WordPress expects when booting without a web request.
 */
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_HOST']   = 'falscher-hase-ddev.ddev.site'; // Change to your project's domain. If you are using DDEV, check config.yaml.
$_SERVER['REQUEST_URI'] = '';
$_SERVER['SERVER_PORT'] = '80';

/**
 * Loads WordPress.
 */
$wp_load_path = dirname(__DIR__) . '/../../../wp-load.php';
if ( ! file_exists($wp_load_path)) {
    throw new RuntimeException('wp-load.php could not be found at ' . $wp_load_path);
}
require_once $wp_load_path;
