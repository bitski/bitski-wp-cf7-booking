<?php
/**
 * Plugin REST API gateway.
 *
 * Registers REST API routes for the plugin.
 *
 * Routes are registered conditionally based on the plugin config options.
 * Each route is registered with a callback function that handles the request.
 * The callback function is responsible for fetching and returning the data needed for the route.
 *
 * @since 0.3.0
 */

namespace BitskiWPCF7Booking\rest;

use BitskiWPCF7Booking\core\Options;
use WP_REST_Request;
use WP_REST_Response;

class Api
{
    /**
     * Initializes plugin REST API gateway.
     */
    public function init(): void
    {
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
    }

    /**
     * Registers REST API routes.
     */
    public function registerRestRoutes(): void
    {
        // Registers the 'health' route.
        register_rest_route('bitski-wp-cf7-booking/v1', '/health', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleHealthEndpoint'],
            'permission_callback' => '__return_true'
        ]);

        // Conditionally registers the 'example-rest-feature' REST route if the feature is enabled in config options.
        if (Options::get('bitski-wp-cf7-booking/option/example-rest-feature')) {
            register_rest_route('bitski-wp-cf7-booking/v1', '/example-rest-feature', [
                'methods'             => 'GET',
                'callback'            => [$this, 'handleExampleRestFeatureEndpoint'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'param' => [
                        'type'              => 'string',
                        'required'          => false,
                        'description'       => 'Example query parameter.',
                        'validate_callback' => static function ($value) {
                            return is_string($value) && ! empty($value);
                        },
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ]
            ]);
        }
        // Add additional endpoints here as needed.
    }

    /**
     * Handles the health endpoint.
     *
     * @since 0.4.1
     */
    public function handleHealthEndpoint(): WP_REST_Response
    {
        return $this->createJsonResponse('ok', 'Plugin is healthy.');
    }

    /**
     * Example REST feature endpoint handler.
     *
     * Returns a basic JSON response.
     *
     * @param  WP_REST_Request  $request
     *
     * @return WP_REST_Response
     */
    public function handleExampleRestFeatureEndpoint(WP_REST_Request $request): WP_REST_Response
    {
        // Extracts the 'param' request parameter with default.
        $param = $request->get_param('param') ?: 'default-value';

        // Returns a JSON response with the 'param' value.
        return $this->createJsonResponse('ok', 'Example REST endpoint reached successfully.', ['param' => $param,]);
    }

    /**
     * Factory for WP_REST_Response.
     *
     * Generates a standardized JSON REST API response with status, message, timestamp, and optional data.
     *
     * @since 0.4.1
     */
    private function createJsonResponse(string $status, string $message, array $data = []): WP_REST_Response
    {
        return new WP_REST_Response([
            'status'    => $status,
            'message'   => $message,
            'timestamp' => time(),
            'data'      => $data
        ]);
    }
}
