<?php
/**
 * Page template for the plugin admin settings page.
 *
 * Contains a Settings API form and an admin-post action form.
 * Displays Settings API admin notices automatically.
 *
 * Loaded via the Admin class.
 *
 * @since 0.2.1
 */

// Exits if accessed directly.
if ( ! defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <?php
    $this->renderH1(); ?>

    <!-- Settings API notices. -->
    <?php
    settings_errors(BITSKI_WP_CF7_BOOKING_SLUG . '_options'); ?>

    <!-- Settings API form (stores plugin options). -->
    <form action="options.php" method="post">
        <?php
        // Displays hidden fields required by the WordPress Settings API.
        settings_fields(BITSKI_WP_CF7_BOOKING_SLUG . '_options_group');

        // Displays the registered settings fields for this page by calling their render methods from the Admin class. ?>
        <div class="field bitski-wp-cf7-booking-enable-plugin">
            <?php
            $this->renderEnablePluginField(); ?>
        </div>
        <div class="field bitski-wp-cf7-booking-h1-color">
            <?php
            $this->renderH1ColorField(); ?>
        </div>
        <div class="field bitski-wp-cf7-booking-submit-button-label">
            <?php
            $this->renderSubmitButtonLabelField(); ?>
        </div>

        <?php
        // Displays a submit button.
        submit_button(esc_html($this->options['admin_option_submit_button_label'] ?? 'Submit')); ?>
    </form>

    <!-- Admin action form (resets plugin options). -->
    <form action="<?php
    echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="<?php
        echo esc_attr(BITSKI_WP_CF7_BOOKING_SLUG . '_reset_options'); ?>">
        <?php
        wp_nonce_field(
                BITSKI_WP_CF7_BOOKING_SLUG . '_reset_options',
                BITSKI_WP_CF7_BOOKING_SLUG . '_nonce'
        ); ?>
        <button type="submit" <?php
        echo $this->isPluginEnabled() ? ' disabled' : ''; ?>>Reset options to defaults
        </button>
        <span class="description">Only available when the plugin is disabled.</span>
    </form>
</div>
