<?php

/**
 * Plugin unit tests bootstrap.
 *
 * Loads the Composer autoloader.
 *
 * @since 0.4.3
 */

require_once __DIR__ . '/unit/stubs/WPCF7_Submission.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Minimal WordPress function stubs for unit tests.
 * WordPress itself is intentionally not loaded in unit tests.
 */
if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field(string $text): string
    {
        return trim(strip_tags($text));
    }
}

if (!function_exists('sanitize_email')) {
    function sanitize_email(string $email): string
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }
}

if (!function_exists('sanitize_textarea_field')) {
    function sanitize_textarea_field(string $text): string
    {
        return trim(strip_tags($text));
    }
}
