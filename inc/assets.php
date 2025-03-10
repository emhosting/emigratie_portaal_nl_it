<?php
function ep_enqueue_assets() {
    wp_enqueue_style( 'ep-style', plugin_dir_url(__FILE__) . '../assets/css/style.css' );
    wp_enqueue_script( 'ep-script', plugin_dir_url(__FILE__) . '../assets/js/script.js', array('jquery', 'jquery-ui-sortable'), '1.0', true );
    wp_localize_script( 'ep-script', 'ajax_object', array( 'ajax_url' => admin_url('admin-ajax.php') ) );
}

function ep_enqueue_admin_assets() {
    wp_enqueue_style( 'ep-admin-style', plugin_dir_url(__FILE__) . '../assets/css/ep-admin.css' );
    wp_enqueue_script( 'ep-admin-script', plugin_dir_url(__FILE__) . '../assets/js/ep-admin.js', array('jquery'), '1.0', true );
    wp_localize_script( 'ep-admin-script', 'ajax_object', array( 'ajax_url' => admin_url('admin-ajax.php') ) );
}

add_action('wp_enqueue_scripts', 'ep_enqueue_assets');
add_action('admin_enqueue_scripts', 'ep_enqueue_admin_assets');
?>