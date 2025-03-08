<?php
function ep_enqueue_assets() {
    // Laad CSS-bestanden
    wp_enqueue_style('ep-admin-style', plugin_dir_url(__FILE__) . '../assets/css/ep-admin.css');
    wp_enqueue_style('ep-style', plugin_dir_url(__FILE__) . '../assets/css/style.css');

    // Laad JavaScript-bestanden
    wp_enqueue_script('ep-admin-js', plugin_dir_url(__FILE__) . '../assets/js/ep-admin.js', array('jquery'), null, true);
    wp_enqueue_script('ep-frontend-js', plugin_dir_url(__FILE__) . '../assets/js/frontend.js', array('jquery'), null, true);
    wp_enqueue_script('ep-script', plugin_dir_url(__FILE__) . '../assets/js/script.js', array('jquery'), null, true);
    wp_enqueue_script('ep-user-actions', plugin_dir_url(__FILE__) . '../assets/js/user-actions.js', array('jquery'), null, true);

    // Gebruik wp_add_inline_script om variabele data aan ep-script mee te geven
    $ajax_data = 'var ajax_object = { "ajaxurl": "' . admin_url('admin-ajax.php') . '" };';
    wp_add_inline_script('ep-script', $ajax_data);
}
add_action('wp_enqueue_scripts', 'ep_enqueue_assets');
add_action('admin_enqueue_scripts', 'ep_enqueue_assets');
?>