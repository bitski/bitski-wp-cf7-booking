<?php

// Exits if accessed directly.
if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * Plugin bootstrap and class initialization.
 *
 * Loads the Composer autoloader if available.
 * Instantiates and initializes all core and feature classes.
 * Conditionally instantiates and initializes classes based on plugin options.
 * Logs autoloader and class instantiation errors without breaking the plugin.
 *
 * @since 0.1.0
 */
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    error_log('Autoloader not found: ' . __DIR__ . '/vendor/autoload.php');
}

/**
 * Array of core and feature plugin classes to be initialized automatically, can be extended or modified as needed.
 *
 * Core and feature classes that are initialized unconditionally.
 *
 * Note: The order of the classes in this array determines the initialization order.
 * Classes earlier in the array will be initialized first.
 * Initialization order:
 * - Config     → base configuration
 * - Options    → depends on Config
 * - Setup      → registers core WordPress features
 * - Lifecycle  → handles plugin lifecycle events
 * - Hooks      → attaches runtime hooks
 *
 * @var array $bootstrap_classes
 */
$bootstrap_classes = [
    \BitskiWPCF7Booking\core\Config::class,
    \BitskiWPCF7Booking\core\Options::class,
    \BitskiWPCF7Booking\core\Setup::class,
    \BitskiWPCF7Booking\core\Lifecycle::class,
    \BitskiWPCF7Booking\core\Hooks::class,
];

/**
 * Array of conditional classes that are only initialized if the corresponding plugin option is enabled.
 *
 * Each entry maps a filter name to the class that should be instantiated.
 * Filter keys enable/disable optional plugin features via plugin options.
 *
 * @var array $conditional_class_map
 */
$conditional_class_map = [
    'bitski-wp-cf7-booking/option/assets/load'   => \BitskiWPCF7Booking\assets\AssetsLoader::class,
    'bitski-wp-cf7-booking/option/rest/api/load' => \BitskiWPCF7Booking\rest\Api::class,
];

/**
 * Array of conditional integration classes that are only initialized if the corresponding plugin option is enabled.
 *
 * Each entry maps a filter name to a class and optional dependencies to be injected into its constructor.
 * Filter keys enable/disable optional plugin features via plugin options.
 *
 * @var array $integration_class_map
 */
$integration_class_map = [
    'bitski-wp-cf7-booking/option/integration/cf7adapter/load' =>
        [
            'class'        => \BitskiWPCF7Booking\integration\CF7Adapter::class,
            'dependencies' => [
                \BitskiWPCF7Booking\application\BookingService::class,
                \BitskiWPCF7Booking\integration\dev\MailGuard::class,
            ]
        ]
];

/**
 * Array of admin-specific classes that are only initialized if the corresponding plugin option is enabled
 * and the request is in the admin area.
 *
 * Each entry maps a filter name to the class that should be instantiated.
 * Filter keys enable/disable optional plugin features via plugin options.
 *
 * @var array $admin_class_map
 */
$admin_class_map = [
    'bitski-wp-cf7-booking/option/admin/load' => \BitskiWPCF7Booking\admin\Admin::class,
];

/**
 * Instantiates and initializes core and feature classes unconditionally.
 */
foreach ($bootstrap_classes as $class) {
    try {
        $instance = new $class();
        if (method_exists($instance, 'init')) {
            $instance->init();
        }
    } catch (\Throwable $error) {
        error_log($class . ' Error: ' . $error->getMessage());
    }
}

/**
 * Instantiates and initializes conditional classes based on plugin option filters.
 */
foreach ($conditional_class_map as $option_key => $class) {
    if (\BitskiWPCF7Booking\core\Options::get($option_key)) {
        try {
            $instance = new $class();
            if (method_exists($instance, 'init')) {
                $instance->init();
            }
        } catch (\Throwable $error) {
            error_log($class . ' Error: ' . $error->getMessage());
        }
    }
}

/**
 * Instantiates and initializes conditional integration classes based on plugin option filters.
 *
 * Adds dependencies to the class constructor if they are defined in the integration_class_map.
 * Allows simple manual dependency injection for classes that require specific dependencies,
 * including manually wired nested dependencies (e.g. BookingService requires a CapacityManager which requires a ReservationRepository).
 *
 * Dependency wiring is handled centrally in the bootstrap.
 */
add_action('plugins_loaded', static function () use ($integration_class_map) {
    foreach ($integration_class_map as $option_key => $definition) {
        if (\BitskiWPCF7Booking\core\Options::get($option_key)) {
            try {
                $class                = $definition['class'];
                $dependencies         = $definition['dependencies'] ?? [];
                $dependency_instances = [];

                foreach ($dependencies as $dependency) {
                    if ($dependency === \BitskiWPCF7Booking\application\BookingService::class) {
                        $reservation_repository = new \BitskiWPCF7Booking\infrastructure\ReservationRepository();
                        $capacity_manager       = new \BitskiWPCF7Booking\domain\CapacityManager(
                            $reservation_repository
                        );

                        $dependency_instances[] = new $dependency($reservation_repository, $capacity_manager);

                        continue;
                    }

                    $dependency_instances[] = new $dependency();
                }

                $instance = new $class(...$dependency_instances);
                if (method_exists($instance, 'init')) {
                    $instance->init();
                }
            } catch (\Throwable $error) {
                error_log($class . ' Error: ' . $error->getMessage());
            }
        }
    }
});

/**
 * Instantiates and initializes conditional admin-specific classes based on plugin option filters.
 *
 * Only runs if the request is in the admin area and not an AJAX request.
 */
if (is_admin() && ! wp_doing_ajax()) {
    foreach ($admin_class_map as $option_key => $class) {
        if (\BitskiWPCF7Booking\core\Options::get($option_key)) {
            try {
                $instance = new $class();
                if (method_exists($instance, 'init')) {
                    $instance->init();
                }
            } catch (\Throwable $error) {
                error_log($class . ' Error: ' . $error->getMessage());
            }
        }
    }
}
