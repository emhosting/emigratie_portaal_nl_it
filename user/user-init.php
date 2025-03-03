<?php
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue front-end scripts and styles
function ep_enqueue_frontend_assets() {
    // CSS files
    wp_enqueue_style('emigratie-portaal-style', EP_PLUGIN_URL . 'assets/css/style.css', array(), EP_PLUGIN_VERSION);
    wp_enqueue_style('emigratie-portaal-dashboard', EP_PLUGIN_URL . 'assets/css/dashboard.css', array('emigratie-portaal-style'), EP_PLUGIN_VERSION);
    // JavaScript files (include jQuery UI for drag & drop)
    wp_enqueue_script('emigratie-portaal-script', EP_PLUGIN_URL . 'assets/js/script.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable'), EP_PLUGIN_VERSION, true);
    wp_enqueue_script('emigratie-portaal-ajax', EP_PLUGIN_URL . 'assets/js/ajax.js', array('jquery', 'emigratie-portaal-script'), EP_PLUGIN_VERSION, true);
    // Localize script with AJAX URL and nonce, plus translation strings for JS
    wp_localize_script('emigratie-portaal-ajax', 'EmigratieAjax', array(
        'ajax_url'       => admin_url('admin-ajax.php'),
        'nonce'          => wp_create_nonce('emigratie_nonce'),
        'completed_text' => __('completed', 'emigratie-portaal'),
        'not_set_text'   => __('not set', 'emigratie-portaal')
    ));
}
add_action('wp_enqueue_scripts', 'ep_enqueue_frontend_assets');

// Register shortcodes for front-end dashboard and checklist
function ep_register_shortcodes() {
    add_shortcode('emigratie_dashboard', 'ep_dashboard_shortcode');
    add_shortcode('emigratie_checklist', 'ep_checklist_shortcode');
}
add_action('init', 'ep_register_shortcodes');
