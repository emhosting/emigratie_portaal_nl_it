<?php
/*
Plugin Name: Emigratie Portaal
Plugin URI: https://example.com/emigratie-portaal
Description: A portal for managing an emigration checklist with categories, tasks, and progress tracking.
Version: 1.0
Author: Example Author
Author URI: https://example.com
Text Domain: emigratie-portaal
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('EP_PLUGIN_VERSION', '1.0');
define('EP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load text domain for translations
function ep_load_textdomain() {
    load_plugin_textdomain('emigratie-portaal', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'ep_load_textdomain');

// Include core functionality files
require_once EP_PLUGIN_DIR . 'includes/functions.php';
require_once EP_PLUGIN_DIR . 'includes/checklist-functions.php';
require_once EP_PLUGIN_DIR . 'includes/checklist-data.php';
require_once EP_PLUGIN_DIR . 'includes/ajax-functions.php';

// Include admin or user-specific files
if (is_admin()) {
    require_once EP_PLUGIN_DIR . 'admin/install.php';
    require_once EP_PLUGIN_DIR . 'admin/settings.php';
    require_once EP_PLUGIN_DIR . 'admin/admin-init.php';
} else {
    require_once EP_PLUGIN_DIR . 'user/dashboard.php';
    require_once EP_PLUGIN_DIR . 'user/checklist.php';
    require_once EP_PLUGIN_DIR . 'user/user-init.php';
}

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'ep_portal_activate');
register_deactivation_hook(__FILE__, 'ep_portal_deactivate');
