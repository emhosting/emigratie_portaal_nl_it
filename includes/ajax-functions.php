<?php
if (!defined('ABSPATH')) {
    exit;
}

// AJAX: Get tasks for a category (for category selection)
function ep_ajax_get_tasks() {
    check_ajax_referer('emigratie_nonce');
    $cat_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $tasks_html = ep_render_tasks($cat_id);
    wp_send_json_success(array('tasks_html' => $tasks_html));
}
add_action('wp_ajax_ep_get_tasks', 'ep_ajax_get_tasks');
add_action('wp_ajax_nopriv_ep_get_tasks', 'ep_ajax_get_tasks');

// AJAX: Update task order within a category after sorting
function ep_ajax_update_task_order() {
    check_ajax_referer('emigratie_nonce');
    $cat_id = isset($_POST['category']) ? intval($_POST['category']) : 0;
    if ($cat_id && isset($_POST['order']) && is_array($_POST['order'])) {
        $order = array_map('intval', $_POST['order']);
        foreach ($order as $index => $task_id) {
            // Ensure task is in the correct category and update its order
            wp_set_post_terms($task_id, array($cat_id), 'emigratie_category');
            wp_update_post(array('ID' => $task_id, 'menu_order' => $index));
        }
    }
    wp_send_json_success();
}
add_action('wp_ajax_ep_update_task_order', 'ep_ajax_update_task_order');
add_action('wp_ajax_nopriv_ep_update_task_order', 'ep_ajax_update_task_order');

// AJAX: Mark a task as completed or not completed
function ep_ajax_mark_complete() {
    check_ajax_referer('emigratie_nonce');
    $task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
    $completed = (isset($_POST['completed']) && $_POST['completed'] === '1') ? '1' : '0';
    if ($task_id) {
        update_post_meta($task_id, 'completed', $completed);
    }
    // Recalculate progress
    $percent = ep_get_progress();
    wp_send_json_success(array('progress' => $percent));
}
add_action('wp_ajax_ep_mark_complete', 'ep_ajax_mark_complete');
add_action('wp_ajax_nopriv_ep_mark_complete', 'ep_ajax_mark_complete');

// AJAX: Save updated start and end dates for a task
function ep_ajax_save_task_dates() {
    check_ajax_referer('emigratie_nonce');
    $task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
    $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : '';
    $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : '';
    if ($task_id) {
        update_post_meta($task_id, 'start_date', $start_date);
        update_post_meta($task_id, 'end_date', $end_date);
    }
    wp_send_json_success();
}
add_action('wp_ajax_ep_save_task_dates', 'ep_ajax_save_task_dates');
add_action('wp_ajax_nopriv_ep_save_task_dates', 'ep_ajax_save_task_dates');

// AJAX: Move a task to a different category (dragging to category)
function ep_ajax_move_task() {
    check_ajax_referer('emigratie_nonce');
    $task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
    $new_cat = isset($_POST['new_cat']) ? intval($_POST['new_cat']) : 0;
    if ($task_id && $new_cat) {
        // Assign the task to the new category
        wp_set_post_terms($task_id, array($new_cat), 'emigratie_category');
        // Place it at end of new category's list (highest menu_order)
        $tasks_in_cat = get_posts(array(
            'post_type'  => 'emigratie_task',
            'tax_query'  => array(array('taxonomy' => 'emigratie_category', 'field' => 'term_id', 'terms' => $new_cat)),
            'numberposts' => -1
        ));
        $new_order = $tasks_in_cat ? count($tasks_in_cat) - 1 : 0;
        wp_update_post(array('ID' => $task_id, 'menu_order' => $new_order));
    }
    wp_send_json_success();
}
add_action('wp_ajax_ep_move_task', 'ep_ajax_move_task');
add_action('wp_ajax_nopriv_ep_move_task', 'ep_ajax_move_task');

// AJAX: Update category order after dragging categories
function ep_ajax_update_cat_order() {
    check_ajax_referer('emigratie_nonce');
    if (isset($_POST['order'])) {
        $order_data = json_decode(stripslashes($_POST['order']), true);
        if (is_array($order_data)) {
            update_option('ep_category_order', array_map('intval', $order_data));
        }
    }
    wp_send_json_success();
}
add_action('wp_ajax_ep_update_cat_order', 'ep_ajax_update_cat_order');
add_action('wp_ajax_nopriv_ep_update_cat_order', 'ep_ajax_update_cat_order');
