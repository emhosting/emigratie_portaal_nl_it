<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$user_id = get_current_user_id();
$table_name = $wpdb->prefix . 'emigratie_checklist';

// Haal de taken op
$tasks = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = $user_id ORDER BY id ASC");

echo '<div class="checklist-container">';
echo '<h2>Mijn Checklist</h2>';
echo '<ul id="task-list">';
if ($tasks) {
    foreach ($tasks as $task) {
        $status_class = '';
        if ($task->status == 'not_started') {
            $status_class = 'status-not-started';
        } elseif ($task->status == 'in_progress') {
            $status_class = 'status-in-progress';
        } elseif ($task->status == 'completed') {
            $status_class = 'status-completed';
        }

        echo '<li data-id="' . esc_attr($task->id) . '" class="task-item ' . $status_class . '">';
        echo esc_html($task->task);
        echo '<span class="task-notes">' . esc_html($task->notes) . '</span>';
        if (!empty($task->attachment)) {
            echo '<a href="' . esc_url($task->attachment) . '" target="_blank">' . __('View Attachment', 'emigratie-portaal') . '</a>';
        }
        echo '</li>';
    }
} else {
    echo '<li>Geen taken gevonden.</li>';
}
echo '</ul>';
echo '</div>';

// Shortcode handler for the checklist page (categories and tasks)
function ep_checklist_shortcode() {
    // Fetch all categories (terms)
    $categories = get_terms(array('taxonomy' => 'emigratie_category', 'hide_empty' => false));
    if (is_wp_error($categories)) {
        $categories = array();
    }
    // Order categories by custom saved order if available
    $order_opt = get_option('ep_category_order');
    if ($order_opt && is_array($order_opt)) {
        usort($categories, function($a, $b) use ($order_opt) {
            $posA = array_search($a->term_id, $order_opt);
            $posB = array_search($b->term_id, $order_opt);
            if ($posA === false) $posA = PHP_INT_MAX;
            if ($posB === false) $posB = PHP_INT_MAX;
            return $posA - $posB;
        });
    }
    // Determine the active category (selected category)
    $active_cat = null;
    if (isset($_GET['category'])) {
        $slug = sanitize_text_field($_GET['category']);
        $term = get_term_by('slug', $slug, 'emigratie_category');
        if ($term && !is_wp_error($term)) {
            $active_cat = $term;
        }
    }
    if (!$active_cat && !empty($categories)) {
        $active_cat = $categories[0];
    }
    // Start output buffering and include the template for checklist UI
    ob_start();
    include EP_PLUGIN_DIR . 'templates/checklist-template.php';
    return ob_get_clean();
}

// AJAX handler for updating task status
function ep_update_task_status() {
    $task_id = intval($_POST['task_id']);
    $status = sanitize_text_field($_POST['status']);
    if ($task_id && $status) {
        update_post_meta($task_id, 'task_status', $status);
        wp_send_json_success();
    }
    wp_send_json_error();
}
add_action('wp_ajax_ep_update_task_status', 'ep_update_task_status');

// AJAX handler for adding attachments
function ep_add_task_attachment() {
    $task_id = intval($_POST['task_id']);
    if (!empty($_FILES['attachment']['name'])) {
        $uploaded_file = wp_handle_upload($_FILES['attachment'], array('test_form' => false));
        if ($uploaded_file && !isset($uploaded_file['error'])) {
            update_post_meta($task_id, 'task_attachment', $uploaded_file['url']);
            wp_send_json_success(array('url' => $uploaded_file['url']));
        }
    }
    wp_send_json_error();
}
add_action('wp_ajax_ep_add_task_attachment', 'ep_add_task_attachment');

// AJAX handler for adding notes
function ep_add_task_note() {
    $task_id = intval($_POST['task_id']);
    $note = sanitize_textarea_field($_POST['note']);
    if ($task_id && $note) {
        update_post_meta($task_id, 'task_note', $note);
        wp_send_json_success();
    }
    wp_send_json_error();
}
add_action('wp_ajax_ep_add_task_note', 'ep_add_task_note');
