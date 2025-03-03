<?php
if (!defined('ABSPATH')) {
    exit;
}

// Activation hook: set up custom data and pages
function ep_portal_activate() {
    // Ensure custom post types and taxonomies are registered
    ep_register_post_types();
    flush_rewrite_rules();

    // Only run first-time setup if not already installed
    if (!get_option('ep_installed')) {
        // Load default categories and tasks
        include_once EP_PLUGIN_DIR . 'includes/checklist-data.php';
        global $ep_default_categories, $ep_default_tasks;
        if (!empty($ep_default_categories) && !empty($ep_default_tasks)) {
            // Create default category terms
            foreach ($ep_default_categories as $cat_name) {
                if (!term_exists($cat_name, 'emigratie_category')) {
                    wp_insert_term($cat_name, 'emigratie_category', array('slug' => sanitize_title($cat_name)));
                }
            }
            // Create default tasks and assign to categories
            foreach ($ep_default_tasks as $cat_name => $tasks) {
                $term = get_term_by('name', $cat_name, 'emigratie_category');
                if ($term && !is_wp_error($term)) {
                    $cat_id = $term->term_id;
                    $order_index = 0;
                    foreach ($tasks as $task) {
                        $post_id = wp_insert_post(array(
                            'post_type'   => 'emigratie_task',
                            'post_title'  => $task['name'],
                            'post_status' => 'publish',
                            'menu_order'  => $order_index
                        ));
                        if ($post_id) {
                            wp_set_post_terms($post_id, array($cat_id), 'emigratie_category');
                            if (!empty($task['start'])) {
                                update_post_meta($post_id, 'start_date', $task['start']);
                            }
                            if (!empty($task['end'])) {
                                update_post_meta($post_id, 'end_date', $task['end']);
                            }
                            update_post_meta($post_id, 'completed', '0');
                            $order_index++;
                        }
                    }
                }
            }
        }
        // Create front-end pages for dashboard and checklist if they do not exist
        if (!get_page_by_path('emigratie-portaal')) {
            wp_insert_post(array(
                'post_title'   => 'Emigratie Portaal',
                'post_name'    => 'emigratie-portaal',
                'post_content' => '[emigratie_dashboard]',
                'post_status'  => 'publish',
                'post_type'    => 'page'
            ));
        }
        if (!get_page_by_path('emigratie-checklist')) {
            wp_insert_post(array(
                'post_title'   => 'Emigratie Checklist',
                'post_name'    => 'emigratie-checklist',
                'post_content' => '[emigratie_checklist]',
                'post_status'  => 'publish',
                'post_type'    => 'page'
            ));
        }
        // Mark as installed to avoid duplicate setup on reactivation
        update_option('ep_installed', 1);
    }
}

// Deactivation hook: cleanup rewrite rules
function ep_portal_deactivate() {
    flush_rewrite_rules();
}
