<?php
/**
 * Plugin Name: Emigratie Portaal
 * Description: Plugin voor het beheren van taken en categorieën.
 * Version: 1.0
 * Author: Your Name
 */

defined( 'ABSPATH' ) || exit;

// Include de benodigde bestanden
include_once plugin_dir_path(__FILE__) . 'inc/admin-menu.php';
include_once plugin_dir_path(__FILE__) . 'inc/ajax-handlers.php';
include_once plugin_dir_path(__FILE__) . 'inc/assets.php';
include_once plugin_dir_path(__FILE__) . 'inc/dashboard.php';
include_once plugin_dir_path(__FILE__) . 'inc/database.php';
include_once plugin_dir_path(__FILE__) . 'inc/frontend.php';
include_once plugin_dir_path(__FILE__) . 'inc/functions.php';
include_once plugin_dir_path(__FILE__) . 'inc/init.php';
include_once plugin_dir_path(__FILE__) . 'inc/manage-attachments.php';
include_once plugin_dir_path(__FILE__) . 'inc/manage-categories.php';
include_once plugin_dir_path(__FILE__) . 'inc/manage-tasks.php';
include_once plugin_dir_path(__FILE__) . 'inc/user-actions.php';
include_once plugin_dir_path(__FILE__) . 'inc/user-interface.php';
?>