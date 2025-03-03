<?php
if (!defined('ABSPATH')) {
    exit;
}

// Register custom post type "Taak" and taxonomy "Categorie"
function ep_register_post_types() {
    // Custom Post Type: emigratie_task (Tasks)
    $labels = array(
        'name'               => __('Tasks', 'emigratie-portaal'),
        'singular_name'      => __('Task', 'emigratie-portaal'),
        'add_new'            => __('Add New', 'emigratie-portaal'),
        'add_new_item'       => __('Add New Task', 'emigratie-portaal'),
        'edit_item'          => __('Edit Task', 'emigratie-portaal'),
        'new_item'           => __('New Task', 'emigratie-portaal'),
        'view_item'          => __('View Task', 'emigratie-portaal'),
        'search_items'       => __('Search Tasks', 'emigratie-portaal'),
        'not_found'          => __('No tasks found', 'emigratie-portaal'),
        'not_found_in_trash' => __('No tasks found in Trash', 'emigratie-portaal'),
        'all_items'          => __('All Tasks', 'emigratie-portaal'),
        'archives'           => __('Task Archives', 'emigratie-portaal'),
        'insert_into_item'   => __('Insert into task', 'emigratie-portaal'),
        'uploaded_to_this_item' => __('Uploaded to this task', 'emigratie-portaal'),
        'menu_name'          => __('Emigratie Tasks', 'emigratie-portaal')
    );
    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => 'emigratie-portaal',  // place under plugin menu
        'supports'           => array('title', 'editor', 'page-attributes'),
        'hierarchical'       => false,
        'has_archive'        => false,
        'rewrite'            => false,
        'capability_type'    => 'post'
    );
    register_post_type('emigratie_task', $args);

    // Taxonomy: emigratie_category (Categories)
    $labels = array(
        'name'              => __('Categories', 'emigratie-portaal'),
        'singular_name'     => __('Category', 'emigratie-portaal'),
        'search_items'      => __('Search Categories', 'emigratie-portaal'),
        'all_items'         => __('All Categories', 'emigratie-portaal'),
        'edit_item'         => __('Edit Category', 'emigratie-portaal'),
        'update_item'       => __('Update Category', 'emigratie-portaal'),
        'add_new_item'      => __('Add New Category', 'emigratie-portaal'),
        'new_item_name'     => __('New Category Name', 'emigratie-portaal'),
        'menu_name'         => __('Categories', 'emigratie-portaal')
    );
    $args = array(
        'labels'            => $labels,
        'public'            => false,
        'show_ui'           => true,
        'show_in_menu'      => false, // will appear under CPT in menu
        'hierarchical'      => true,
        'rewrite'           => false
    );
    register_taxonomy('emigratie_category', 'emigratie_task', $args);
}
add_action('init', 'ep_register_post_types');

// Calculate overall progress percentage of completed tasks
function ep_get_progress() {
    $count = wp_count_posts('emigratie_task');
    if (!$count || !isset($count->publish) || $count->publish == 0) {
        return 0;
    }
    $total = (int)$count->publish;
    $done_query = new WP_Query(array(
        'post_type'  => 'emigratie_task',
        'meta_key'   => 'completed',
        'meta_value' => '1'
    ));
    $completed = (int)$done_query->found_posts;
    $percent = ($total > 0) ? round(($completed / $total) * 100) : 0;
    return $percent;
}
function emigratie_checklist_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'user/checklist.php';
    return ob_get_clean();
}
add_shortcode('emigratie_checklist', 'emigratie_checklist_shortcode');

function emigratie_enqueue_jquery() {
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'emigratie_enqueue_jquery');
