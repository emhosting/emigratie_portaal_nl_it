<?php
function ep_add_category() {
    global $wpdb;
    $name = sanitize_text_field($_POST['name']);
    $result = $wpdb->insert("{$wpdb->prefix}ep_categories", array('name' => $name, 'user_id' => 0));
    if ($result !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error(array('message' => 'Categorie kon niet worden toegevoegd.'));
    }
    wp_die();
}

add_action('wp_ajax_add_category', 'ep_add_category');
?>