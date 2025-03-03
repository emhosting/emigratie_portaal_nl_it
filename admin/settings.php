<?php
if (!defined('ABSPATH')) {
    exit;
}

// Render the settings page content
function ep_render_settings_page() {
    $message = '';
    // Handle form submission for settings
    if (isset($_POST['submit'])) {
        check_admin_referer('ep_settings');
        $welcome = isset($_POST['ep_welcome_text']) ? sanitize_textarea_field($_POST['ep_welcome_text']) : '';
        update_option('ep_welcome_text', $welcome);
        $message = '<div class="notice notice-success is-dismissible"><p>' . __('Settings saved.', 'emigratie-portaal') . '</p></div>';
    }
    // Include the HTML template for the settings page
    include EP_PLUGIN_DIR . 'templates/settings-template.php';
}
