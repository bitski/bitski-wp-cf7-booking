<?php
/**
 * Plugin admin interface.
 *
 * Handles plugin admin screens and options.
 *
 * @since 0.2.0
 */

namespace BitskiWPCF7Booking\admin;

class Admin
{
    /**
     * Default plugin options used as fallback values.
     *
     * @since 0.2.3
     */
    private array $defaultOptions = [
            'admin_option_enable_plugin'       => 0,
            'admin_option_h1_color'            => '#0073aa',
            'admin_option_submit_button_label' => 'Submit'
    ];

    /**
     * Current plugin options loaded from storage and merged with defaults.
     *
     * @since 0.2.3
     */
    private array $options = [];

    /**
     * Initializes plugin admin interface.
     */
    public function init(): void
    {
        add_action('admin_menu', [$this, 'addSettingsPage']);
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_post_' . BITSKI_WP_CF7_BOOKING_SLUG . '_reset_options', [$this, 'resetOptions']);
        add_action('admin_notices', [$this, 'displayNonPersistentAdminNotices']);

        $savedOptions = get_option(BITSKI_WP_CF7_BOOKING_SLUG . '_options', []);
        $savedOptions = is_array($savedOptions) ? $savedOptions : [];

        $this->options = array_replace($this->defaultOptions, $savedOptions);
    }

    /**
     * Getter for the option if the plugin is enabled.
     *
     * @since 0.2.3
     */
    private function isPluginEnabled(): bool
    {
        return ! empty($this->options['admin_option_enable_plugin']);
    }

    /**
     * Adds a plugin settings page as a submenu item to the Settings menu.
     */
    public function addSettingsPage(): void
    {
        add_submenu_page(
                'options-general.php',
                BITSKI_WP_CF7_BOOKING_SLUG . ' Settings',
                BITSKI_WP_CF7_BOOKING_SLUG . ' Settings',
                'manage_options',
                BITSKI_WP_CF7_BOOKING_SLUG . '-settings',
                [$this, 'displaySettingsPage']
        );
    }

    /**
     * Registers the plugin settings.
     *
     * Uses the WordPress Settings API to register a settings group and its associated options.
     * The settings group is named after the plugin slug with '_options_group' appended.
     * The options are named after the plugin slug with '_options' appended.
     * The options are stored together in a single database entry in the 'wp_options' table,
     * using the plugin slug with '_options' appended as the option name.
     *
     * @since 0.2.2
     */
    public function registerSettings(): void
    {
        register_setting(
                BITSKI_WP_CF7_BOOKING_SLUG . '_options_group',
                BITSKI_WP_CF7_BOOKING_SLUG . '_options',
                [
                        'type'              => 'array',
                        'sanitize_callback' => [$this, 'sanitizeOptions'],
                ]
        );
    }

    /**
     * Sanitizes the plugin options.
     * This method is called when the plugin options are saved.
     *
     * 1. Converts the 'admin_option_enable_plugin' option to 1 or 0.
     * 2. Sanitizes the 'admin_option_h1_color' option using sanitize_hex_color().
     * 3. Sanitizes the 'admin_option_submit_button_label' option using sanitize_text_field().
     */
    public function sanitizeOptions($input): array
    {
        $input = is_array($input) ? $input : [];

        // Sanitizes options.
        return [
                'admin_option_enable_plugin'       => ! empty($input['admin_option_enable_plugin']) ? 1 : 0,
                'admin_option_h1_color'            => sanitize_hex_color(
                        $input['admin_option_h1_color'] ?? ''
                ) ?: $this->defaultOptions['admin_option_h1_color'],
                'admin_option_submit_button_label' => sanitize_text_field(
                        $input['admin_option_submit_button_label'] ?? $this->defaultOptions['admin_option_submit_button_label']
                ),
        ];
    }

    private function renderH1() {
        echo '<h1 style="color:'.$this->options['admin_option_h1_color'].';">'.esc_html(BITSKI_WP_CF7_BOOKING_SLUG).' Settings</h1>';
    }

    /**
     * Renders the "Enable plugin" checkbox field.
     */
    private function renderEnablePluginField(): void
    { ?>
        <label for="<?php
        echo esc_attr(BITSKI_WP_CF7_BOOKING_SLUG . '_options[admin_option_enable_plugin]'); ?>">Enable
            plugin</label>
        <input type="checkbox"
               name="<?php
               echo esc_attr(BITSKI_WP_CF7_BOOKING_SLUG . '_options[admin_option_enable_plugin]'); ?>"
               value="1"
                <?php
                checked($this->options['admin_option_enable_plugin'], 1); ?> />
        <span class="description">Enable or disable the plugin.</span>
        <?php
    }

    /**
     * Renders the "H1 color" color picker field.
     *
     * @since 0.2.3
     */
    private function renderH1ColorField(): void
    { ?>
        <label for="<?php
        echo esc_attr(BITSKI_WP_CF7_BOOKING_SLUG . '_options[admin_option_h1_color]'); ?>">H1 color</label>
        <input type="color"
               name="<?php
               echo esc_attr(BITSKI_WP_CF7_BOOKING_SLUG . '_options[admin_option_h1_color]'); ?>"
               value="<?php
               echo esc_attr($this->options['admin_option_h1_color']); ?>"
                <?php
                echo ! $this->isPluginEnabled() ? ' disabled' : ''; ?>
        />
        <?php
    }

    /**
     * Renders the "Submit button label" text field.
     */
    private function renderSubmitButtonLabelField(): void
    { ?>
        <label for="<?php
        echo esc_attr(BITSKI_WP_CF7_BOOKING_SLUG . '_options[admin_option_submit_button_label]'); ?>">Submit button
            label</label>
        <input type="text"
               name="<?php
               echo esc_attr(BITSKI_WP_CF7_BOOKING_SLUG . '_options[admin_option_submit_button_label]'); ?>"
               value="<?php
               echo esc_attr($this->options['admin_option_submit_button_label']); ?>"
               class="regular-text"
                <?php
                echo ! $this->isPluginEnabled() ? ' disabled' : ''; ?>
        />
        <span class="description">Set the submit button label.</span>
        <?php
    }

    /**
     * Adds a non-persistent admin notice.
     *
     * Stores notice in a transient for the next page load.
     *
     * @since 0.4.0
     */
    private function addNonPersistentAdminNotice(string $message, string $type = 'info'): void
    {
        if ( ! in_array($type, ['success', 'error', 'warning', 'info'], true)) {
            $type = 'info';
        }

        $transientName = BITSKI_WP_CF7_BOOKING_SLUG . '_admin_notices';

        $notices = get_transient($transientName);
        if ( ! is_array($notices)) {
            $notices = [];
        }

        $notices[] = ['message' => $message, 'type' => $type];

        set_transient(
                $transientName,
                $notices,
                30 // 30 seconds lifetime, usually enough for redirect
        );
    }

    /**
     * Displays all manually added, non-persistent admin notices.
     *
     * @since 0.4.0
     */
    public function displayNonPersistentAdminNotices(): void
    {
        $transientName = BITSKI_WP_CF7_BOOKING_SLUG . '_admin_notices';
        $notices       = get_transient($transientName);

        //
        if ($notices && is_array($notices)) {
            foreach ($notices as $notice) {
                echo '<div class="notice notice-' . esc_attr(
                                $notice['type']
                        ) . ' is-dismissible"><p><strong>' . esc_html(
                             $notice['message']
                     ) . '</strong></p></div>';
            }

            delete_transient($transientName);
        }
    }

    /**
     * Resets the plugin options to their default values.
     *
     * @since 0.2.4
     */
    public function resetOptions(): void
    {
        $nonceName   = BITSKI_WP_CF7_BOOKING_SLUG . '_nonce';
        $nonceAction = BITSKI_WP_CF7_BOOKING_SLUG . '_reset_options';
        $baseUrl     = admin_url('options-general.php?page=' . BITSKI_WP_CF7_BOOKING_SLUG . '-settings');

        // Guard clauses: blocks unauthorized requests immediately.
        if ( ! current_user_can('manage_options') ||
             ! isset($_POST[$nonceName]) ||
             ! wp_verify_nonce(
                     $_POST[$nonceName],
                     $nonceAction
             )) {
            $this->addNonPersistentAdminNotice('You are not authorized to perform this action.', 'error');

            // Redirects to the settings page with the error query parameter.
            wp_safe_redirect($baseUrl);
            exit;
        }

        // Resets options to default values.
        update_option(BITSKI_WP_CF7_BOOKING_SLUG . '_options', $this->defaultOptions);

        $this->addNonPersistentAdminNotice('Options have been reset to defaults.', 'success');

        // Redirects to the settings page with the success query parameter.
        wp_safe_redirect($baseUrl);
        exit;
    }

    /**
     * Displays the plugin settings page.
     */
    public function displaySettingsPage(): void
    {
        // Checks user capabilities.
        if ( ! current_user_can('manage_options')) {
            return;
        }

        // Displays the settings page.
        include_once BITSKI_WP_CF7_BOOKING_PATH . 'templates/pages/admin/settings.php';
    }
}
