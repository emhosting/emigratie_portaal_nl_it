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

// Add status options to the admin view
function ep_add_task_status_meta_box() {
    add_meta_box(
        'ep_task_status',
        __('Task Status', 'emigratie-portaal'),
        'ep_task_status_meta_box_callback',
        'emigratie_task',
        'side'
    );
}
add_action('add_meta_boxes', 'ep_add_task_status_meta_box');

function ep_task_status_meta_box_callback($post) {
    $status = get_post_meta($post->ID, 'task_status', true);
    ?>
    <label for="task_status"><?php _e('Status:', 'emigratie-portaal'); ?></label>
    <select name="task_status" id="task_status">
        <option value="not_started" <?php selected($status, 'not_started'); ?>><?php _e('Not Started', 'emigratie-portaal'); ?></option>
        <option value="in_progress" <?php selected($status, 'in_progress'); ?>><?php _e('In Progress', 'emigratie-portaal'); ?></option>
        <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'emigratie-portaal'); ?></option>
    </select>
    <?php
}

function ep_save_task_status_meta_box($post_id) {
    if (array_key_exists('task_status', $_POST)) {
        update_post_meta($post_id, 'task_status', $_POST['task_status']);
    }
}
add_action('save_post', 'ep_save_task_status_meta_box');
