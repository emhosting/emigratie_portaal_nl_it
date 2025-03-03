<?php
// Exit if accessed directly
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete all custom posts of type emigratie_task
$tasks = get_posts(array('post_type' => 'emigratie_task', 'numberposts' => -1, 'post_status' => 'any'));
if (!empty($tasks)) {
    foreach ($tasks as $task) {
        wp_delete_post($task->ID, true);
    }
}

// Delete all terms in emigratie_category taxonomy
$terms = get_terms(array('taxonomy' => 'emigratie_category', 'hide_empty' => false));
if (!empty($terms) && !is_wp_error($terms)) {
    foreach ($terms as $term) {
        wp_delete_term($term->term_id, 'emigratie_category');
    }
}

// Delete plugin options
delete_option('ep_installed');
delete_option('ep_welcome_text');
delete_option('ep_category_order');
