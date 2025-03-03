<?php
if (!defined('ABSPATH')) {
    exit;
}

// Add admin menu pages for the plugin
function ep_add_admin_pages() {
    add_menu_page(
        __('Emigratie Portaal', 'emigratie-portaal'),
        __('Emigratie Portaal', 'emigratie-portaal'),
        'manage_options',
        'emigratie-portaal',
        'ep_render_settings_page',
        'dashicons-admin-home',
        6
    );
    // The custom post type "Taken" will appear under this menu via show_in_menu in CPT registration
}
add_action('admin_menu', 'ep_add_admin_pages');
