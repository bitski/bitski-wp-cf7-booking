<?php

/** Centralized plugin configuration class.
 *
 * Provides global plugin options and CSS class mappings.
 * Use `Options::get()` within plugin classes,
 * or `apply_filters()` within templates to retrieve values.
 *
 * @since 0.1.3
 */

namespace BitskiWPCF7Booking\core;

class Config
{
    /**
     * Global plugin options.
     *
     * Key: option name / filter name
     * Value: global option value
     *
     * Usage:
     * - Plugin core: Options::get('option-name', 'local-override-value')
     * - Templates: apply_filters('option-name', 'local-override-value')
     */
    public static array $options = [
        // Example: 'option-name' => 'global-value'

        // Admin settings page
        'bitski-wp-cf7-booking/option/admin/load'                       => true,

        // AssetsLoader
        'bitski-wp-cf7-booking/option/assets/load'                      => false,

        // Example REST feature
        'bitski-wp-cf7-booking/option/example-rest-feature'             => false,

        // REST API options
        'bitski-wp-cf7-booking/option/rest/api/load'                    => true,

        // Integration with external plugins or modules - example adapter
        'bitski-wp-cf7-booking/option/integration/example-adapter/load' => false,

        // Add more option filters as needed.
    ];

    /**
     * Global CSS class mappings.
     *
     * Key: filter name
     * Value: array of global CSS classes
     *
     * Usage: apply_filters('filter-name', ['local-class'])
     */
    public static array $classes = [
        // Example: 'filter-name' => [ 'class1', 'class2' ]

        // Add more class name filters as needed
    ];

    /**
     * Intentionally left empty.
     *
     * Config is a static data provider.
     */
    public function init(): void
    {
    }
}
