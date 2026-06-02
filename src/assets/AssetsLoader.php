<?php
/**
 * Plugin assets loader.
 *
 * @since 0.6.0
 */

namespace BitskiWPCF7Booking\assets;

class AssetsLoader
{
    /**
     * Initializes assets loader.
     */
    public function init(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontendAssets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueueBlockEditorAssets']);
    }

    /**
     * Enqueues plugin frontend assets.
     */
    public function enqueueFrontendAssets(): void
    {
        // Determines main frontend CSS & JS files, prefers minified versions if available.
        $pluginFrontendMainStyle = file_exists(
            BITSKI_WP_CF7_BOOKING_PATH . '/assets/css/main.min.css'
        ) ? 'main.min.css' : 'main.css';
        $pluginFrontendMainScript = file_exists(
            BITSKI_WP_CF7_BOOKING_PATH . '/assets/js/main.min.js'
        ) ? 'main.min.js' : 'main.js';

        // CSS
        //
        // Main plugin CSS
        if (file_exists(BITSKI_WP_CF7_BOOKING_PATH . '/assets/css/' . $pluginFrontendMainStyle)) {
            wp_enqueue_style(
                'bitski-wp-cf7-booking-frontend-style',
                BITSKI_WP_CF7_BOOKING_URL . '/assets/css/' . $pluginFrontendMainStyle,
                [],
                BITSKI_WP_CF7_BOOKING_VERSION
            );
        }

        // Scripts
        //
        // Main plugin JS (ESM module)
        // Requires WordPress 6.5 or higher for native wp_enqueue_script_module() support.
        if (file_exists(BITSKI_WP_CF7_BOOKING_PATH . '/assets/js/' . $pluginFrontendMainScript)) {
            wp_enqueue_script_module(
                'bitski-wp-cf7-booking-frontend-script',
                BITSKI_WP_CF7_BOOKING_URL . '/assets/js/' . $pluginFrontendMainScript,
                [],
                BITSKI_WP_CF7_BOOKING_VERSION,
                [
                    'in_footer' => true,
                ]
            );
        }
    }

    /**
     * Enqueues plugin admin assets.
     */
    public function enqueueAdminAssets(): void
    {
        // Determines main admin CSS file, prefers minified version if available.
        $pluginAdminMainStyle = file_exists(
            BITSKI_WP_CF7_BOOKING_PATH . '/assets/css/admin.min.css'
        ) ? 'admin.min.css' : 'admin.css';

        // CSS
        //
        // Main plugin admin CSS
        if (file_exists(BITSKI_WP_CF7_BOOKING_PATH . '/assets/css/' . $pluginAdminMainStyle)) {
            wp_enqueue_style(
                'bitski-wp-cf7-booking-admin-style',
                BITSKI_WP_CF7_BOOKING_URL . '/assets/css/' . $pluginAdminMainStyle,
                [],
                BITSKI_WP_CF7_BOOKING_VERSION
            );
        }
    }

    /**
     * Enqueues plugin block editor assets.
     *
     * @since 0.10.1
     */
    public function enqueueBlockEditorAssets(): void
    {
        // CSS
        //
        // Block editor style
        if (file_exists(BITSKI_WP_CF7_BOOKING_PATH . '/assets/css/editor.css')) {
            wp_enqueue_style(
                'bitski-wp-cf7-booking-block-editor-style',
                BITSKI_WP_CF7_BOOKING_URL . '/assets/css/editor.css',
                [],
                BITSKI_WP_CF7_BOOKING_VERSION
            );
        }
    }
}
